<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      4.0
 *
 * @package    Email_Subscribers
 * @subpackage Email_Subscribers/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines Shortcode
 *
 * @package    Email_Subscribers
 * @subpackage Email_Subscribers/public
 */
class ES_Shortcode {

	/**
	 * Unique form identifier based on number of forms rendered on the page
	 * 
	 * @var string
	 * 
	 * @since 4.7.5
	 */
	public static $form_identifier;

	/**
	 * Variable to store form submission response
	 *
	 * @var array
	 * 
	 * @since 4.7.5
	 */
	public static $response = array();

	public function __construct() {
	}

	public static function render_es_subscription_shortcode( $atts ) {
		ob_start();

		$atts = shortcode_atts( array(
			'namefield' => '',
			'desc'      => '',
			'group'     => ''
		), $atts, 'email-subscribers' );

		$data['name_visible'] = $atts['namefield'];
		$data['list_visible'] = 'no';
		$data['lists']        = array();
		$data['form_id']      = 0;
		$data['list']         = $atts['group'];
		$data['desc']         = $atts['desc'];

		self::render_form( $data );

		return ob_get_clean();
	}

	/**
	 * Render Subscription form using ES 4.0+ Shortcode
	 *
	 * @param $atts
	 *
	 * @return false|string
	 */
	public static function render_es_form( $atts ) {
		ob_start();

		$atts = shortcode_atts( array( 'id' => '' ), $atts, 'email-subscribers-form' );

		$id = $atts['id'];

		if ( ! empty( $id ) ) {
			$form = ES()->forms_db->get_form_by_id( $id );

			if ( $form ) {

				$form_data = ES_Forms_Table::get_form_data_from_body( $form );

				self::render_form( $form_data );
			}
		}

		return ob_get_clean();
	}

	// Handle Email Subscribers Group Selector Shortcode
	// Backward Compatibility
	public static function render_es_advanced_form( $atts ) {
		ob_start();

		$atts = shortcode_atts( array(
			'id' => ''
		), $atts, 'email-subscribers-advanced-form' );

		$af_id = $atts['id'];

		if ( ! empty( $af_id ) ) {
			$form = ES()->forms_db->get_form_by_af_id( $af_id );
			if ( $form ) {
				$form_data = ES_Forms_Table::get_form_data_from_body( $form );

				self::render_form( $form_data );
			}
		}

		return ob_get_clean();
	}

	public static function render_form( $data ) {

		/**
		 * - Show name? -> Prepare HTML for name
		 * - Show email? -> Prepare HTML for email // Always true
		 * - Show lists? -> Preapre HTML for Lists list_ids
		 * - Hidden Field -> form_id,
		 *      list,
		 *      es_email_page,
		 *      es_email_page_url,
		 *      es-subscribe,
		 *      honeypot field
		 */
		// Compatibility for GDPR
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		$show_name          = ! empty( $data['name_visible'] ) ? strtolower( $data['name_visible'] ) : false;
		$required_name      = ! empty( $data['name_required'] ) ? $data['name_required'] : false;
		$name_label         = ! empty( $data['name_label'] ) ? $data['name_label'] : '';
		$name_place_holder  = ! empty( $data['name_place_holder'] ) ? $data['name_place_holder'] : '';
		$email_label        = ! empty( $data['email_label'] ) ? $data['email_label'] : '';
		$email_place_holder = ! empty( $data['email_place_holder'] ) ? $data['email_place_holder'] : '';
		$button_label       = ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'Subscribe', 'email-subscribers' );
		$list_label         = ! empty( $data['list_label'] ) ? $data['list_label'] : __( 'Select list(s)', 'email-subscribers' );
		$show_list          = ! empty( $data['list_visible'] ) ? $data['list_visible'] : false;
		$list_ids           = ! empty( $data['lists'] ) ? $data['lists'] : array();
		$form_id            = ! empty( $data['form_id'] ) ? $data['form_id'] : 0;
		$list               = ! empty( $data['list'] ) ? $data['list'] : 0;
		$desc               = ! empty( $data['desc'] ) ? $data['desc'] : '';
		$form_version       = ! empty( $data['form_version'] ) ? $data['form_version'] : '0.1';
		$gdpr_consent       = ! empty( $data['gdpr_consent'] ) ? $data['gdpr_consent'] : 'no';
		$gdpr_consent_text  = ! empty( $data['gdpr_consent_text'] ) ? $data['gdpr_consent_text'] : '';
		$allowedtags 		= ig_es_allowed_html_tags_in_esc();

		/**
		 * We did not have $email_label, $name_label in
		 * ES < 4.2.2
		 *
		 * Since ES 4.2.2, we are adding form_version in form settings.
		 *
		 * If we don't find Form Version in settings, we are setting as 0.1
		 *
		 * So, if form_version is 0.1 then set default label
		 */
		if ( '0.1' == $form_version ) {
			$email_label = __( 'Email', 'email-subscribers' );
			$name_label  = __( 'Name', 'email-subscribers' );
		}

		self::$form_identifier = self::generate_form_identifier( $form_id );

		$submitted_name    = '';
		$submitted_email   = '';
		$message_class     = '';
		$message_text      = '';
		$selected_list_ids = array();

		if ( self::is_posted() ) {
			// self::$response is set by ES_Handle_Subscription::handle_subscription() when subscription form is posted
			$response = ! empty( self::$response ) ? self::$response: array();
			if ( ! empty( $response ) ) {
				$message_class = ! empty( $response['status'] ) && 'SUCCESS' === $response['status'] ? 'success' : 'error';
				$message_text  = ! empty( $response['message_text'] ) ? $response['message_text'] : '';
			}

			$submitted_name       = ig_es_get_post_data( 'esfpx_name' );
			$submitted_email      = ig_es_get_post_data( 'esfpx_email' );
			$selected_list_hashes = ig_es_get_post_data( 'esfpx_lists' );

			if ( ! empty( $selected_list_hashes ) ) {
				$selected_lists = ES()->lists_db->get_lists_by_hash( $selected_list_hashes );
				if ( $selected_lists ) {
					$selected_list_ids = array_column( $selected_lists, 'id' );
				}
			}
		} else {
			if ( is_user_logged_in() ) {
				$prefill_form = apply_filters( 'ig_es_prefill_subscription_form', 'yes' );
				if ( 'yes' === $prefill_form ) {
					$current_user    = wp_get_current_user();
					$submitted_email = $current_user->user_email;
					$submitted_name  = $current_user->user_firstname . ' ' . $current_user->user_lastname;
				}
			}
		}

		//replace total contact
		$total_contacts = ES()->contacts_db->count_active_contacts_by_list_id();
		$desc           = str_replace( '{{TOTAL-CONTACTS}}', $total_contacts, $desc );

		$current_page     = get_the_ID();
		$current_page_url = get_the_permalink( $current_page );

		$unique_id = uniqid();
		$hp_style  = 'position:absolute;top:-99999px;' . ( is_rtl() ? 'right' : 'left' ) . ':-99999px;z-index:-99;';
		$nonce     = wp_create_nonce( 'es-subscribe' );

		// Name
		$name_html = '';
		$required  = '';
		if ( ! empty( $show_name ) && 'no' !== $show_name ) {
			if ( 'yes' === $required_name ) {
				$required = 'required';
				if ( ! empty( $name_label ) ) {
					$name_label .= '*';
				}
			}
			$name_html .= '<div class="es-field-wrap"><label>' . $name_label . '<br/><input type="text" name="esfpx_name" class="ig_es_form_field_name"  placeholder="' . $name_place_holder . '" value="' . $submitted_name . '" ' ;

			/* Adding required="required" as attribute name, value pair because wp_kses will strip off the attribute if only 'required' attribute is provided. */
			$name_html .= 'required' === $required ? 'required = "' . $required . '"' : '';
			$name_html .= '/></label></div>';
		}

		// Lists
		if ( ! empty( $list_ids ) && $show_list ) {
			$lists_id_name_map = ES()->lists_db->get_list_id_name_map();
			$lists_id_hash_map = ES()->lists_db->get_list_id_hash_map( $list_ids );
			$list_html         = self::prepare_lists_checkboxes( $lists_id_name_map, $list_ids, 1, $selected_list_ids, $list_label, 0, 'esfpx_lists[]', $lists_id_hash_map );
		} elseif ( ! empty( $list_ids ) && ! $show_list ) {
			$list_html = '';
			$lists     = ES()->lists_db->get_lists_by_id( $list_ids );
			if ( ! empty( $lists ) ) {
				foreach ( $lists as $list ) {
					if ( ! empty( $list ) && ! empty( $list['hash'] ) ) {
						$list_html .= '<input type="hidden" name="esfpx_lists[]" value="' . $list['hash'] . '" />';
					}
				}
			}
		} elseif ( is_numeric( $list ) ) {
			$lists = ES()->lists_db->get_lists_by_id( $list );
			$list_html = '';
			if ( ! empty( $lists ) ) {
				$list_hash = ! empty( $lists[0]['hash'] ) ? $lists[0]['hash'] : '';
				if ( ! empty( $list_hash ) ) {
					$list_html = '<input type="hidden" name="esfpx_lists[]" value="' . $list_hash . '" />';
				}
			}
		} else {
			$list_data = ES()->lists_db->get_list_by_name( $list );
			if ( empty( $list_data ) ) {
				$list_id = ES()->lists_db->add_list( $list );
			} else {
				$list_id = $list_data['id'];
			}

			$lists = ES()->lists_db->get_lists_by_id( $list_id );
			$list_html = '';
			if ( ! empty( $lists ) ) {
				$list_hash = ! empty( $lists[0]['hash'] ) ? $lists[0]['hash'] : '';
				if ( ! empty( $list_hash ) ) {
					$list_html = '<input type="hidden" name="esfpx_lists[]" value="' . $list_hash . '" />';
				}
			}
		}

		// Form html
		$form_html = '<input type="hidden" name="esfpx_form_id" value="' . $form_id . '" />';

		$email_html = '<div class="es-field-wrap"><label>';
		if ( ! empty( $email_label ) ) {
			$email_html .= $email_label . '*<br/>';
		}
		$email_html .= '<input class="es_required_field es_txt_email ig_es_form_field_email" type="email" name="esfpx_email" value="' . $submitted_email . '" placeholder="' . $email_place_holder . '" required="required"/></label></div>';
		?>

		<div class="emaillist" id="es_form_<?php echo esc_attr( self::$form_identifier ); ?>">
			<?php
			// Don't show form if submission was successful.
			if ( 'success' !== $message_class) {
				$form_action_url = ES_Common::get_current_request_url();
				?>
			<form action="<?php echo esc_url( $form_action_url ); ?>#es_form_<?php echo esc_attr( self::$form_identifier ); ?>" method="post" class="es_subscription_form es_shortcode_form" id="es_subscription_form_<?php echo esc_attr( $unique_id ); ?>" data-source="ig-es">
				<?php if ( '' != $desc ) { ?>
					<div class="es_caption"><?php echo esc_html( $desc ); ?></div>
				<?php } ?>
				<?php
					echo wp_kses( $name_html, $allowedtags );
					echo wp_kses( $email_html, $allowedtags );
					echo wp_kses( $list_html , $allowedtags );
					echo wp_kses( $form_html , $allowedtags ); 
				?>
				<input type="hidden" name="es" value="subscribe" />
				<input type="hidden" name="esfpx_es_form_identifier" value="<?php echo esc_attr( self::$form_identifier ); ?>" />
				<input type="hidden" name="esfpx_es_email_page" value="<?php echo esc_attr( $current_page ); ?>"/>
				<input type="hidden" name="esfpx_es_email_page_url" value="<?php echo esc_url( $current_page_url ); ?>"/>
				<input type="hidden" name="esfpx_status" value="Unconfirmed"/>
				<input type="hidden" name="esfpx_es-subscribe" id="es-subscribe-<?php echo esc_attr( $unique_id ); ?>" value="<?php echo esc_attr( $nonce ); ?>"/>
				<label style="<?php echo esc_attr( $hp_style ); ?>"><input type="email" name="esfpx_es_hp_email" class="es_required_field" tabindex="-1" autocomplete="-1" value=""/></label>
				<?php

				do_action( 'ig_es_after_form_fields', $data );

				if ( 'yes' === $gdpr_consent ) { 
					?>
					<p><input type="checkbox" name="es_gdpr_consent" value="true" required/>&nbsp;<label style="display: inline"><?php echo wp_kses_post( $gdpr_consent_text ); ?></label></p>
					<?php 
				} elseif ( ( in_array( 'gdpr/gdpr.php', $active_plugins ) || array_key_exists( 'gdpr/gdpr.php', $active_plugins ) ) ) {
					GDPR::consent_checkboxes();
				}

				?>
				<input type="submit" name="submit" class="es_subscription_form_submit es_submit_button es_textbox_button" id="es_subscription_form_submit_<?php echo esc_attr( $unique_id ); ?>" value="<?php echo esc_attr( $button_label ); ?>"/>

				<?php $spinner_image_path = ES_PLUGIN_URL . 'lite/public/images/spinner.gif'; ?>

				<span class="es_spinner_image" id="spinner-image"><img src="<?php echo esc_url( $spinner_image_path ); ?>" alt="<?php echo esc_attr__( 'Loading', 'email-subscribers' ); ?>"/></span>

			</form>
			<?php
			}
			?>
			<span class="es_subscription_message <?php echo esc_attr( $message_class ); ?>" id="es_subscription_message_<?php echo esc_attr( $unique_id ); ?>">
			<?php echo esc_html( $message_text ); ?>
			</span>
		</div>

		<?php
	}

	public static function prepare_lists_checkboxes( $lists, $list_ids = array(), $columns = 3, $selected_lists = array(), $list_label = '', $contact_id = 0, $name = 'lists[]', $lists_id_hash_map = array() ) {

		$list_label = ! empty( $list_label ) ? $list_label : __( 'Select list(s)', 'email-subscribers' );
		$lists_html = '<div><p><b class="font-medium text-gray-500 pb-2">' . $list_label . '*</b></p><table class="ig-es-form-list-selection"><tr>';
		$i          = 0;

		if ( ! empty( $contact_id ) ) {
			$list_contact_status_map = ES()->lists_contacts_db->get_list_contact_status_map( $contact_id );
		}

		foreach ( $lists as $list_id => $list_name ) {
			if ( 0 != $i && 0 === ( $i % $columns ) ) {
				$lists_html .= '</tr><tr>';
			}
			$status_span = '';
			if ( in_array( $list_id, $list_ids ) ) {

				// Check if list hash has been passed for given list id, if yes then use list hash, else use list id
				if ( ! empty( $lists_id_hash_map[ $list_id ] ) ) {
					$list_value = $lists_id_hash_map[ $list_id ];
				} else {
					$list_value = $list_id;
				}
				
				if ( in_array( $list_id, $selected_lists ) ) {
					if ( ! empty( $contact_id ) ) {
						$status_span = '<span class="es_list_contact_status ' . $list_contact_status_map[ $list_id ] . '" title="' . ucwords( $list_contact_status_map[ $list_id ] ) . '">';
					}
					$lists_html .= '<td class="pt-4">';
					$lists_html .= $status_span . '<label><input type="checkbox" class="pl-6 form-checkbox" name="' . $name . '" checked="checked" value="' . $list_value . '" /><span class="pl-1 pr-6 text-gray-500 text-sm font-normal">' . $list_name . '</span></label></td>';
				} else {
					$lists_html .= '<td class="pt-4"><label><input type="checkbox" class="pl-6 form-checkbox " name="' . $name . '" value="' . $list_value . '" /><span class="pl-1 pr-6 text-gray-500 text-sm font-normal">' . $list_name . '</span></label></td>';
				}
				$i ++;
			}
		}

		$lists_html .= '</tr></table></div>';

		return $lists_html;
	}

	/**
	 * Generate a unique form identifier based on number of forms already rendered on the page.
	 * 
	 * @return string $form_identifier
	 * 
	 * @since 4.7.5
	 */
	public static function generate_form_identifier( $form_id = 0 ) {
		
		static $form_count = 1;
		
		$form_identifier = '';

		if ( in_the_loop() ) {
			$page_id         = get_the_ID();
			$form_identifier = sprintf( 'f%1$d-p%2$d-n%3$d',
				$form_id,
				$page_id,
				$form_count
			);
		} else {
			$form_identifier = sprintf( 'f%1$d-n%2$d',
				$form_id,
				$form_count
			);
		}

		$form_count++;

		return $form_identifier;
	}

	/**
	 * Get form's identifier
	 * 
	 * @return string
	 * 
	 * @since 4.7.5
	 */
	public static function get_form_identifier() {
		return self::$form_identifier;
	}

	/**
	 * Return true if this form is the same one as currently posted.
	 * 
	 * @return boolean
	 * 
	 * @since 4.7.5
	 */
	public static function is_posted() {

		$form_identifier = ig_es_get_request_data( 'esfpx_es_form_identifier' );
		if ( empty( $form_identifier ) ) {
			return false;
		}

		return self::get_form_identifier() === $form_identifier;
	}
}


