<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Faq{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/faq/block.json';
		$metadata = json_decode( ob_get_clean(), true );

		register_block_type(
			$metadata['name'],
			array(
				'attributes'  	  => $metadata['attributes'],
				'render_callback' => [ $this, 'render_content' ],
				'script'		  => 'woolentor-accordion-min',
			)
		);

	}

	public function render_content( $settings, $content ){

		$uniqClass = 'woolentorblock-'.$settings['blockUniqId'];
		$areaClasses = array( $uniqClass, 'woolentorfaq-block-area' );
		$classes = array( 'htwoolentor-faq' );

		!empty( $settings['align'] ) ? $areaClasses[] = 'align'.$settings['align'] : '';
		!empty( $settings['className'] ) ? $areaClasses[] = $settings['className'] : '';


		!empty( $settings['iconPosition'] ) ? $classes[] = 'woolentorfaq-icon-pos-'.$settings['iconPosition'] : '';


		$icon = '<span class="htwoolentor-faq-head-indicator"></span>';

		$accordion_settings = [
            'showitem' => ( $settings['showFirstItem'] === true ),
        ];
		$dataOptions = 'data-settings='.wp_json_encode( $accordion_settings );

		ob_start();
		?>
			<div class="<?php echo implode(' ', $areaClasses ); ?>">
				<div class="<?php echo implode(' ', $classes ); ?>" id="<?php echo 'htwoolentor-faq-'.$settings['blockUniqId'] ?>" <?php echo $dataOptions; ?>>

					<?php
						foreach ( $settings['faqList'] as $item ):
							
							$title = ( !empty( $item['title'] ) ? '<span class="htwoolentor-faq-head-text">'.$item['title'].'</span>' : '' );

						?>
							<div class="htwoolentor-faq-card">
                                <?php
									if( $settings['iconPosition'] == 'right'){
										echo sprintf( '<div class="htwoolentor-faq-head">%2$s %1$s</div>',$icon, $title );
									}else{
										echo sprintf( '<div class="htwoolentor-faq-head">%1$s %2$s</div>',$icon, $title );
									}
                                ?>
                                <div class="htwoolentor-faq-body">
                                    <div class="htwoolentor-faq-content">
										<?php 
											if ( !empty( $item['content'] ) ) {
												echo wp_kses_post( $item['content'] );
											}
										?>
                                    </div>
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
WooLentorBlocks_Faq::instance();