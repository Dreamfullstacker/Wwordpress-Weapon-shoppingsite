<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Image_Marker{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/image-marker/block.json';
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

		$uniqClass = 'woolentorblock-'.$settings['blockUniqId'];
		$areaClasses = array( 'woolentor-marker-area' );
		$classes = array( $uniqClass, 'wlb-marker-wrapper' );

		!empty( $settings['align'] ) ? $areaClasses[] = 'align'.$settings['align'] : '';

		!empty( $settings['className'] ) ? $classes[] = $settings['className'] : '';
		!empty( $settings['style'] ) ? $classes[] = 'wlb-marker-style-'.$settings['style'] : 'wlb-marker-style-1';

		$background_image = woolentorBlocks_Background_Control( $settings, 'bgProperty' );

		ob_start();
		?>
			<div class="<?php echo implode(' ', $areaClasses ); ?>">
				<div class="<?php echo implode(' ', $classes ); ?>" style="<?php echo $background_image; ?> position:relative;">

					<?php
						foreach ( $settings['markerList'] as $item ):
							
							$horizontalPos = !empty( $item['horizontal'] ) ? 'left:'.$item['horizontal'].'%;' : 'left:50%;';
							$verticlePos = !empty( $item['verticle'] ) ? 'top:'.$item['verticle'].'%;' : '15%;';

						?>
							<div class="wlb_image_pointer" style="<?php echo $horizontalPos.$verticlePos; ?>">
								<div class="wlb_pointer_box">
									<?php
										if( !empty( $item['title'] ) ){
											echo '<h4>'.esc_html__( $item['title'], 'woolentor' ).'</h4>';
										}
										if( !empty( $item['content'] ) ){
											echo '<p>'.esc_html__( $item['content'], 'woolentor' ).'</p>';
										}
									?>
								</div>
							</div>
							
						<?php
						endforeach;
					?> 
            	</div>
			</div>

		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Image_Marker::instance();