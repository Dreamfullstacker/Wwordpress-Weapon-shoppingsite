<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Store_Feature{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/store-feature/block.json';
		$metadata = json_decode( ob_get_clean(), true );

		register_block_type(
			$metadata['name'],
			array(
				'attributes'  => $metadata['attributes'],
				'render_callback' => [ $this, 'render_content' ]
			)
		);

	}

	public function render_content( $settings, $content ){
		
		$uniqClass = 'woolentorblock-'.$settings['blockUniqId'];
		$areaClasses = array('woolentor-store-feature-area');
		$classes = array( $uniqClass, 'woolentor-blocks ht-feature-wrap' );

		!empty( $settings['align'] ) ? $areaClasses[] = 'align'.$settings['align'] : '';

		!empty( $settings['className'] ) ? $classes[] = $settings['className'] : '';
        !empty( $settings['layout'] ) ? $classes[] = 'ht-feature-style-'.$settings['layout'] : 'ht-feature-style-1';
        !empty( $settings['textAlignment'] ) ? $classes[] = 'woolentor-text-align-'.$settings['textAlignment'] : 'woolentor-text-align-center';

		$store_image = !empty( $settings['featureImage']['id'] ) ? wp_get_attachment_image( $settings['featureImage']['id'], 'full' ) : '';

		ob_start();
		?>
			<div class="<?php echo implode(' ', $areaClasses ); ?>">
				<div class="<?php echo implode(' ', $classes ); ?>">
					<div class="ht-feature-inner">
						<?php
							if( !empty( $store_image ) ){
								echo '<div class="ht-feature-img">'.$store_image.'</div>';
							}
						?>
						<div class="ht-feature-content">
							<?php
								if( !empty( $settings['title'] ) ){
									echo '<h4>'.$settings['title'].'</h4>';
								}
								if( !empty( $settings['subTitle'] ) ){
									echo '<p>'.$settings['subTitle'].'</p>';
								}
							?>
						</div>
					</div>
				</div>
			</div>
		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Store_Feature::instance();