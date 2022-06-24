<?php  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require( WOOLENTOR_ADDONS_PL_PATH. 'includes/admin/include/class.template_cpt.php' );
require( WOOLENTOR_ADDONS_PL_PATH. 'includes/admin/include/template-library/manager.php' );

class Woolentor_Template_Manager{

    const CPTTYPE = 'woolentor-template';
	const CPT_META = 'woolentor_template_meta';

    private static $_instance = null;
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct(){
		Woolentor_Template_CPT::instance();

        //Add Menu
        add_action( 'admin_menu', [ $this, 'admin_menu' ], 225 );
		
        // Load Scripts
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        // Print template edit popup.
		add_action( 'admin_footer', [ $this, 'print_popup' ] );

		// Template type column.
		add_action( 'manage_' . self::CPTTYPE . '_posts_columns', [ $this, 'manage_columns' ] );
		add_action( 'manage_' . self::CPTTYPE . '_posts_custom_column', [ $this, 'columns_content' ], 10, 2 );

        // Print template tabs.
		add_filter( 'views_edit-' . self::CPTTYPE, [ $this, 'print_tabs' ] );

		// query filter
		add_filter( 'parse_query', [ $this, 'query_filter' ] );

        // Template store ajax action
		add_action( 'wp_ajax_woolentor_template_store', [ $this, 'template_store_request' ] );

		// Get template data Ajax action
		add_action( 'wp_ajax_woolentor_get_template', [ $this, 'get_post_By_id' ] );

		// Manage Template Default Status
		add_action( 'wp_ajax_woolentor_manage_default_template', [ $this, 'manage_template_status' ] );

		// Template Import
		add_action( 'wp_ajax_woolentor_import_template', [ $this, 'template_import' ] );
		
    }

    /**
	 * [admin_menu] Add Post type Submenu
	 *
	 * @return void
	 */
    public function admin_menu(){
        $link_custom_post = 'edit.php?post_type=' . self::CPTTYPE;
		add_submenu_page(
			'woolentor_page',
			esc_html__('Template Builder', 'woolentor'),
			esc_html__('Template Builder', 'woolentor'),
			'manage_options',
			$link_custom_post,
            NULL
		);
    }

	/**
	 * Manage Post Table columns
	 *
	 * @param [array] $columns
	 * @return array
	 */
	public function manage_columns( $columns ) {

		$column_author  = $columns['author'];
		$column_date 	= $columns['date'];

		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns['type'] 		= esc_html__('Type', 'woolentor');
		$columns['setdefault'] 	= esc_html__('Default', 'woolentor');
		$columns['author'] 		= esc_html( $column_author );
		$columns['date'] 		= esc_html( $column_date );

		return $columns;
	}

	/**
	 * Manage Custom column content
	 *
	 * @param [string] $column_name
	 * @param [int] $post_id
	 * @return void
	 */
	public function columns_content( $column_name, $post_id ) {
		$tmpType = get_post_meta( $post_id, 'woolentor_template_meta_type', true );

		if( !array_key_exists( $tmpType, self::get_template_type() ) ){
			return;
		}

		// Tabs Group
		if( strpos( $tmpType, 'cart' ) !== false ){
			$tmpTypeGroup = 'cart';
		}else if( strpos( $tmpType, 'myaccount' ) !== false ){
			$tmpTypeGroup = 'myaccount';
		}else if( strpos( $tmpType, 'checkout' ) !== false ){
			$tmpTypeGroup = 'checkout';
		}else{
			$tmpTypeGroup = $tmpType;
		}

		if( $column_name === 'type' ){
			$tabs = '';
			echo isset( self::get_template_type()[$tmpType] ) ? '<a class="column-tmptype" href="edit.php?post_type='.self::CPTTYPE.'&template_type='.$tmpType.'&tabs='.$tmpTypeGroup.'">'.self::get_template_type()[$tmpType]['label'].'</a>' : '-';
		}elseif( $column_name === 'setdefault' ){

			$value = woolentor_get_option( self::get_template_type()[$tmpType]['optionkey'], 'woolentor_woo_template_tabs', '0' );
			$checked = checked( $value, $post_id, false );

			echo '<label class="woolentor-default-tmp-status-switch" id="woolentor-default-tmp-status-'.esc_attr( $tmpType ).'-'.esc_attr( $post_id ).'"><input class="woolentor-status-'.esc_attr( $tmpType ).'" id="woolentor-default-tmp-status-'.esc_attr( $tmpType ).'-'.esc_attr( $post_id ).'" type="checkbox" value="'.esc_attr( $post_id ).'" '.$checked.'/><span><span>'.esc_html__('NO','woolentor').'</span><span>'.esc_html__('YES','woolentor').'</span></span><a>&nbsp;</a></label>';

		}

	}

	/**
	 * Check WooLentor template screen
	 *
	 * @return boolean
	 */
	private function is_current_screen() {
		global $pagenow, $typenow;
		return 'edit.php' === $pagenow && self::CPTTYPE === $typenow;
	}

	/**
	 * Manage Template filter by template type
	 *
	 * @param \WP_Query $query
	 * @return void
	 */
	public function query_filter( \WP_Query $query ) {

		if ( ! is_admin() || ! $this->is_current_screen() || ! empty( $query->query_vars['meta_key'] ) ) {
			return;
		}
		if( isset( $_GET['template_type'] ) && $_GET['template_type'] != '' && $_GET['template_type'] != 'all') {
			$type                              = isset( $_GET['template_type'] ) ? sanitize_key( $_GET['template_type'] ) : '';
			$query->query_vars['meta_key']     = self::CPT_META . '_type';
			$query->query_vars['meta_value']   = $type;
			$query->query_vars['meta_compare'] = '=';
		}

	}

	/**
	 * Get Template Menu Tabs
	 *
	 * @return array
	 */
	public static function get_tabs(){

		$tabs = [
			'shop' => [
				'label' =>__('Shop','woolentor')
			],
			'archive' => [
				'label' =>__('Archive','woolentor')
			],
			'single' => [
				'label' => __('Single','woolentor')
			],
		];

		return apply_filters( 'woolentor_template_menu_tabs', $tabs );

	}

    /**
	 * Get Template Type
	 *
	 * @return array
	 */
	public static function get_template_type(){

		$template_type = [
			'shop' 	=> [
				'label'		=>__('Shop','woolentor'),
				'optionkey'	=> 'productarchivepage'
			],
			'archive' => [
				'label'		=>__('Archive','woolentor'),
				'optionkey'	=>'productallarchivepage'
			],
			'single' => [
				'label' 	=> __('Single','woolentor'),
				'optionkey' => 'singleproductpage'
			],
		];

		return apply_filters( 'woolentor_template_types', $template_type );

	}

	/**
	 * Get sample design from library
	 *
	 * @return array
	 */
	public function get_template_library(){

		// Delete transient data
		if ( get_option( 'woolentor_do_activation_library_cache', FALSE ) ) {
            delete_transient( 'woolentor_template_info' );
			delete_option('woolentor_do_activation_library_cache');
        }

		$get_data = get_transient( 'woolentor_template_info' ) ? get_transient( 'woolentor_template_info' ) : Woolentor_Template_Library_Manager::get_templates_info( true );
		$data = [];

		if( !empty( $get_data['templates'] ) ){
			foreach( $get_data['templates'] as $template ){

				if( $template['shareId'] == 'Shop'){
					$data['shop'][] = $template;
					$data['archive'][] = $template;
				}else if($template['shareId'] == 'Product Details'){
					$data['single'][] = $template;
				}else if($template['shareId'] == 'Cart'){
					$data['cart'][] = $template;
				}else if( $template['shareId'] == 'Checkout Page' ){
					$data['checkout'][] = $template;
				}
				else if( $template['shareId'] == 'My Account' ){
					$data['myaccount'][] = $template;
				}

			}
		}

		return $data;

	}

    /**
	 * Print Template edit popup
	 *
	 * @return void
	 */
	public function print_popup() {
		if( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'woolentor-template' ){
			include_once( WOOLENTOR_ADDONS_PL_PATH. 'includes/admin/templates/template_edit_popup.php' );
		}
    }

    /**
	 * Print Admin Tab
	 *
	 * @param [array] $views
	 * @return array
	 */
    public function print_tabs( $views ) {
		$active_class = 'nav-tab-active';
		$current_type = '';
		if( isset( $_GET['tabs'] ) ){
			$active_class = '';
			$current_type = sanitize_key( $_GET['tabs'] );
		}
        ?>
            <div id="woolentor-template-tabs-wrapper" class="nav-tab-wrapper">
				<div class="woolentor-menu-area">
					<a class="nav-tab <?php echo $active_class; ?>" href="edit.php?post_type=<?php echo self::CPTTYPE; ?>"><?php echo __('All','woolentor');?></a>
					<?php
						foreach( self::get_tabs() as $tabkey => $tab ){
							$active_class = ( $current_type == $tabkey ? 'nav-tab-active' : '' );
							echo '<a class="nav-tab '.$active_class.'" href="edit.php?post_type='.self::CPTTYPE.'&template_type='.$tabkey.'&tabs='.$tabkey.'">'.$tab['label'].'</a>';
						}
					?>
				</div>
				<div class="woolentor-template-importer">
					<button type="button" class="button button-primary">
						<span class="dashicons dashicons-download"></span>
						<span class="woolentor-template-importer-btn-text"><?php esc_html_e('Import Previously Assigned Templates','woolentor');?></span>
					</button>
				</div>
            </div>
			<?php 
				if( !empty( $current_type ) && isset( self::get_tabs()[$current_type]['submenu'] ) ){

					$sub_tab_active_class = 'woolentor-sub-tab-active'; 
					$current_sub_tab = '';
					if( isset( $_GET['tab'] ) ){
						$sub_tab_active_class = '';
						$current_sub_tab = sanitize_key( $_GET['tab'] );
					}

					echo '<div class="woolentor-template-subtabs"><ul>';
						echo '<li><a class="woolentor-sub-tab '.$sub_tab_active_class.'" href="edit.php?post_type='.self::CPTTYPE.'&template_type='.$current_type.'&tabs='.$current_type.'">'.self::get_tabs()[$current_type]['label'].'</a></li>';

						foreach( self::get_tabs()[$current_type]['submenu'] as $subtabkey => $subtab ){
							$sub_tab_active_class = ( $current_sub_tab == $subtabkey ? 'woolentor-sub-tab-active' : '' );
							echo '<li><a class="woolentor-sub-tab '.$sub_tab_active_class.'" href="edit.php?post_type='.self::CPTTYPE.'&template_type='.$subtabkey.'&tabs='.$current_type.'&tab='.$subtabkey.'">'.$subtab['label'].'</a></li>';
						}

					echo '</ul></div>';

				}
			?>
        <?php
		return $views;
    }

    /**
	 * Manage Scripts
	 *
	 * @param [string] $hook
	 * @return void
	 */
    public function enqueue_scripts( $hook ){

        if( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'woolentor-template' ){
            wp_enqueue_style( 'woolentor-template-edit-manager', WOOLENTOR_ADDONS_PL_URL . 'includes/admin/assets/css/template_edit_manager.css' );

			wp_enqueue_style('woolentor-sweetalert', WOOLENTOR_ADDONS_PL_URL . 'includes/admin/assets/lib/css/sweetalert2.min.css');
			wp_enqueue_style('slick', WOOLENTOR_ADDONS_PL_URL . 'assets/css/slick.css' );
			wp_enqueue_script('slick', WOOLENTOR_ADDONS_PL_URL . 'assets/js/slick.min.js', array('jquery'), WOOLENTOR_VERSION, true );

            wp_enqueue_script( 'woolentor-sweetalert', WOOLENTOR_ADDONS_PL_URL . 'includes/admin/assets/lib/js/sweetalert2.min.js', WOOLENTOR_VERSION, true );

            wp_enqueue_script( 'woolentor-template-edit-manager', WOOLENTOR_ADDONS_PL_URL . 'includes/admin/assets/js/template_edit_manager.js', array('jquery', 'wp-util'), WOOLENTOR_VERSION, true );

			$localize_data = [
                'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
				'nonce' 	=> wp_create_nonce('woolentor_tmp_nonce'),
				'templatetype' => self::get_template_type(),
				'templatelist' => $this->get_template_library(),
				'adminURL'	=> admin_url(),
				'labels' => [
					'fields'=>[
						'name'	=> [
							'title' 	  => __('Name','woolentor'),
							'placeholder' => __('Enter a template name','woolentor')
						],
						'type'		 => __('Type','woolentor'),
						'setdefault' => __('Set Default','woolentor'),
					],
					'head' => __('Template Settings','woolentor'),
					'buttons' => [
						'elementor' => [
							'label' => __('Edit With Elementor','woolentor'),
							'link' 	=> '#'
						],
						'gutenberg' => [
							'label' => __('Edit With Gutenberg','woolentor'),
							'link' 	=> '#'
						],
						'save' => [
							'label'  => __('Save Settings','woolentor'),
							'saving' => __('Saving...','woolentor'),
							'saved'  => __('All Data Saved','woolentor'),
							'link' 	 => '#'
						]
					],
					'sampledata' => [
						'visibility' => __('Sample Design','woolentor'),
						'elementor'  => __('Elementor','woolentor'),
						'pro' 		 => __('Pro','woolentor'),
					],
					'importer' =>[
						'button' => [
							'importing' => __('Assigned Template Importing..','woolentor'),
							'imported'  => __('All Assigned Template has been imported','woolentor'),
						],
						'message' =>[
							'title' 	=> __( 'Are you sure?','woolentor' ),
							'message' 	=> __( 'It will import those templates that were created from the "Templates" menu of Elementor and assigned to corresponding WooCommerce pages.','woolentor' ) ,
							'yesbtn' 	=> __('Yes','woolentor'),
							'cancelbtn' => __('Cancel','woolentor') 
						]
					]
				]
            ];
			wp_localize_script( 'woolentor-template-edit-manager', 'WLTMCPT', $localize_data );

        }

    }

    /**
	 * Store Template
	 *
	 * @return int || JSON Data
	 */
	public function template_store_request(){
		if ( isset( $_POST ) ) {

			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'woolentor_tmp_nonce' ) ) {
				$errormessage = array(
					'message'  => __('Nonce Varification Faild !','woolentor')
				);
				wp_send_json_error( $errormessage );
			}

			$title 		= !empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : esc_html__( 'WooLentor template '.time(), 'woolentor' );
			$tmpid 		= !empty( $_POST['tmpId'] ) ? sanitize_text_field( $_POST['tmpId'] ) : '';
			$tmpType 	= !empty( $_POST['tmpType'] ) ? sanitize_text_field( $_POST['tmpType'] ) : 'single';
			$setDefault = !empty( $_POST['setDefault'] ) ? sanitize_text_field( $_POST['setDefault'] ) : 'no';
			$sampleTmpID = !empty( $_POST['sampleTmpID'] ) ? sanitize_text_field( $_POST['sampleTmpID'] ) : '';
			$sampleTmpBuilder = !empty( $_POST['sampleTmpBuilder'] ) ? sanitize_text_field( $_POST['sampleTmpBuilder'] ) : '';

			$data = [
				'title' 		=> $title,
				'id' 			=> $tmpid,
				'tmptype' 		=> $tmpType,
				'setdefaullt'	=> $setDefault,
				'sampletmpid' 	=> $sampleTmpID,
				'sampletmpbuilder' => $sampleTmpBuilder
			];

			if( $tmpid ){
				$this->update( $data );
			}else{
				$this->insert( $data );
			}

		}else{
			$errormessage = array(
				'message'  => __('Post request dose not found','woolentor')
			);
			wp_send_json_error( $errormessage );
		}

	}

    /**
	 * Template Insert
	 *
	 * @param [array] $data
	 * @return JSON
	 */
	public function insert( $data ){

		$args = [
			'post_type'    => self::CPTTYPE,
			'post_status'  => 'publish',
			'post_title'   => $data['title'],
		];
		$new_post_id = wp_insert_post( $args );

		if( $new_post_id ){
			$return = array(
				'message'  => __('Template has been inserted','woolentor'),
				'id'       => $new_post_id,
			);

			// Meta data
			update_post_meta( $new_post_id, self::CPT_META . '_type', $data['tmptype'] );
			update_post_meta( $new_post_id, '_wp_page_template', 'elementor_header_footer' );

			// Sample data import
			if( !empty( $data['sampletmpid'] ) && $data['sampletmpbuilder'] == 'elementor' ){
				$templateurl    = sprintf( Woolentor_Template_Library_Manager::get_api_templateapi(), $data['sampletmpid'] );
				$response_data  = Woolentor_Template_Library_Manager::get_content_remote_request( $templateurl );
				update_post_meta( $new_post_id, '_elementor_data', $response_data['content']['content'] );
				update_post_meta( $new_post_id, '_elementor_edit_mode', 'builder' );
			}

			if( $data['setdefaullt'] == 'yes' ) {
				$this->update_option( 'woolentor_woo_template_tabs' , self::get_template_type()[$data['tmptype']]['optionkey'], $new_post_id );
			}

			wp_send_json_success( $return );

		}else{
			$errormessage = array(
				'message'  => __('Some thing is worng !','woolentor')
			);
			wp_send_json_error( $errormessage );
		}

	}

    /**
	 * Template Update
	 *
	 * @param [array] $data
	 * @return JSON
	 */
	public function update( $data ){

		$update_post_args = array(
			'ID'         => $data['id'],
			'post_title' => $data['title'],
		);
		wp_update_post( $update_post_args );

		// Update Meta data
		update_post_meta( $data['id'], self::CPT_META . '_type', $data['tmptype'] );
		update_post_meta( $data['id'], '_wp_page_template', 'elementor_header_footer' );

		// Sample data import
		if( !empty( $data['sampletmpid'] ) && $data['sampletmpbuilder'] == 'elementor' ){
			$templateurl    = sprintf( Woolentor_Template_Library_Manager::get_api_templateapi(), $data['sampletmpid'] );
			$response_data  = Woolentor_Template_Library_Manager::get_content_remote_request( $templateurl );
			update_post_meta( $data['id'], '_elementor_data', $response_data['content']['content'] );
			update_post_meta( $data['id'], '_elementor_edit_mode', 'builder' );
		}

		if( $data['setdefaullt'] == 'yes' ) {
			$this->update_option( 'woolentor_woo_template_tabs', self::get_template_type()[$data['tmptype']]['optionkey'], $data['id'] );
		}else{
			$this->update_option( 'woolentor_woo_template_tabs', self::get_template_type()[$data['tmptype']]['optionkey'], '0' );
		}

		$return = array(
			'message'  => __('Template has been updated','woolentor'),
			'id'       => $data['id']
		);
		wp_send_json_success( $return );

	}

    /**
	 * Get Template data by id
	 *
	 * @return JSON
	 */
	public function get_post_By_id(){
		if ( isset( $_POST ) ) {

			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'woolentor_tmp_nonce' ) ) {
				$errormessage = array(
					'message'  => __('Nonce Varification Faild !','woolentor')
				);
				wp_send_json_error( $errormessage );
			}

			$tmpid = !empty( $_POST['tmpId'] ) ? sanitize_text_field( $_POST['tmpId'] ) : '';
			$postdata = get_post( $tmpid );
			$tmpType = !empty( get_post_meta( $tmpid, self::CPT_META . '_type', true ) ) ? get_post_meta( $tmpid, self::CPT_META . '_type', true ) : 'single';
			$data = [
				'tmpTitle' 	 => $postdata->post_title,
				'tmpType' 	 => $tmpType,
				'setDefault' => isset( self::get_template_type()[$tmpType]['optionkey'] ) ? woolentor_get_option( self::get_template_type()[$tmpType]['optionkey'], 'woolentor_woo_template_tabs', '0' ) : '0',
			];
            wp_send_json_success( $data );

		}else{
			$errormessage = array(
				'message'  => __('Some thing is worng !','woolentor')
			);
			wp_send_json_error( $errormessage );
		}

	}

	/**
	 * set_default_template_type function
	 *
	 * @return void
	 */
	public function manage_template_status(){

		if ( isset( $_POST ) ) {

			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'woolentor_tmp_nonce' ) ) {
				$errormessage = array(
					'message'  => __('Nonce Varification Faild !','woolentor')
				);
				wp_send_json_error( $errormessage );
			}

			$tmpid 		= !empty( $_POST['tmpId'] ) ? sanitize_text_field( $_POST['tmpId'] ) : '0';
			$tmpType 	= !empty( $_POST['tmpType'] ) ? sanitize_text_field( $_POST['tmpType'] ) : 'single';

			$this->update_option( 'woolentor_woo_template_tabs', self::get_template_type()[$tmpType]['optionkey'], $tmpid );

			$return = array(
				'message'  => __('Template has been updated','woolentor'),
				'id'       => $tmpid
			);

			wp_send_json_success( $return );

		}else{
			$errormessage = array(
				'message'  => __('Some thing is worng !','woolentor')
			);
			wp_send_json_error( $errormessage );
		}

	}

	/**
	 * update_option
	 *
	 * @return void
	 */
	public function update_option( $section, $option_key, $new_value ){
        if( $new_value === Null ){ $new_value = ''; }
        $options_datad = is_array( get_option( $section ) ) ? get_option( $section ) : array();
        $options_datad[$option_key] = $new_value;
        update_option( $section, $options_datad );
    }

	/**
	 * Template Importer
	 *
	 * @return void
	 */
	public function template_import(){
		if ( isset( $_POST ) ) {
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'woolentor_tmp_nonce' ) ) {
				$errormessage = array(
					'message'  => __('Nonce Varification Faild !','woolentor')
				);
				wp_send_json_error( $errormessage );
			}

			foreach( self::get_template_type() as $key => $template_type ){

				$tmp_id = woolentor_get_option( $template_type['optionkey'], 'woolentor_woo_template_tabs', '0' );

				$get_args = array( 
					'p' 		=> $tmp_id, 
					'post_type' => 'elementor_library'
				);
				$templates_query = new \WP_Query( $get_args );
				wp_reset_query();

				if ( $templates_query->have_posts() ) {

					$args = array(
						'ID'        => $tmp_id,
						'post_type' => self::CPTTYPE,
					);
					$update_id = wp_update_post( $args );

					if( ! is_wp_error( $update_id ) ){
						update_post_meta( $update_id, self::CPT_META . '_type', $key );
					}

				}

			}

			$return = array(
				'message'  => __('Template has been imported','woolentor'),
			);

			wp_send_json_success( $return );

		}else{
			$errormessage = array(
				'message'  => __('Some thing is worng !','woolentor')
			);
			wp_send_json_error( $errormessage );
		}

	}


}

Woolentor_Template_Manager::instance();