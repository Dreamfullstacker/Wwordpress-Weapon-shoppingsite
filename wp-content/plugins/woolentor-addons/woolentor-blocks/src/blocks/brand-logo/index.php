<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Brand_Logo{

	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Actions]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * The Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(){

		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Load attributes from block.json.
		ob_start();
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/brand-logo/block.json';
		$metadata = json_decode( ob_get_clean(), true );

		register_block_type(
			$metadata['name'],
			array(
				'attributes'  	  => $metadata['attributes'],
				'render_callback' => [ $this, 'render_content' ]
			)
		);

	}

	public function render_content( $settings, $content ){
		
		$uniqClass 	 = 'woolentorblock-'.$settings['blockUniqId'];
		$classes 	 = array( $uniqClass, 'ht-brand-wrap' );
		$areaClasses = array( 'woolentor-brand-area' );

		!empty( $settings['align'] ) ? $areaClasses[] = 'align'.$settings['align'] : '';

		!empty( $settings['columns']['desktop'] ) ? $classes[] = 'woolentor-columns-'.$settings['columns']['desktop'] : '';
		!empty( $settings['columns']['laptop'] ) ? $classes[] = 'woolentor-laptop-columns-'.$settings['columns']['laptop'] : '';
		!empty( $settings['columns']['tablet'] ) ? $classes[] = 'woolentor-tablet-columns-'.$settings['columns']['tablet'] : '';
		!empty( $settings['columns']['mobile'] ) ? $classes[] = 'woolentor-mobile-columns-'.$settings['columns']['mobile'] : '';

		$default_img = '<img src="'.WOOLENTOR_BLOCK_URL.'/src/assets/images/brand.png'.'" alt="'.esc_html__('Brand Logo','woolentor').'">';
		$brands = $settings['brandLogoList'];

		ob_start();
		
		?>
			<div class="<?php echo implode(' ', $areaClasses ); ?>">
				<div class="<?php echo implode(' ', $classes ); ?>">
					<?php
						$collumval = 'woolentor-col-6';
						if( !empty( $settings['columns']['desktop'] ) ){
							$collumval = 'woolentor-col-'.$settings['columns']['desktop'];
						}
						
						if( is_array( $brands ) ){
							echo '<div class="woolentor-row '.( $settings['noGutter'] === true ? 'wlno-gutters' : '' ).'">';
								foreach ( $brands as $key => $brand ) {
				
									$image = !empty( $brand['image']['id'] ) ? wp_get_attachment_image( $brand['image']['id'], 'full' ) : $default_img;
									$logo  = !empty( $brand['link'] ) ? sprintf('<a href="%s" target="_blank">%s</a>',esc_url( $brand['link'] ), $image ) : $image;
				
									?>
										<div class="<?php echo esc_attr( esc_attr( $collumval ) ); ?>">
											<div class="wl-single-brand">
												<?php echo $logo; ?>
											</div>
										</div>
									<?php
								}
							echo '</div>';
						}
					?>
				</div>
			</div>
		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Brand_Logo::instance();