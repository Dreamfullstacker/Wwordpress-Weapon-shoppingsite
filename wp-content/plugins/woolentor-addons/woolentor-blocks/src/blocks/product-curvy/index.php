<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Product_Curvy{

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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/product-curvy/block.json';
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

		$uniqClass     = 'woolentorblock-'.$settings['blockUniqId'];
		$areaClasses   = array( $uniqClass, 'woocommerce', 'woolentor-product-curvy' );

		!empty( $settings['align'] ) ? $areaClasses[] = 'align'.$settings['align'] : '';
		!empty( $settings['className'] ) ? $areaClasses[] = $settings['className'] : '';

		!empty( $settings['columns']['desktop'] ) ? $areaClasses[] = 'woolentor-columns-'.$settings['columns']['desktop'] : '';
		!empty( $settings['columns']['laptop'] ) ? $areaClasses[] = 'woolentor-laptop-columns-'.$settings['columns']['laptop'] : '';
		!empty( $settings['columns']['tablet'] ) ? $areaClasses[] = 'woolentor-tablet-columns-'.$settings['columns']['tablet'] : '';
		!empty( $settings['columns']['mobile'] ) ? $areaClasses[] = 'woolentor-mobile-columns-'.$settings['columns']['mobile'] : '';

		$queryArgs = [
			'perPage'	=> $settings['perPage'],
			'filterBy'	=> $settings['productFilterType']
		];
		if( $settings['customOrder'] ){
			$queryArgs['orderBy'] = $settings['orderBy'];
			$queryArgs['order'] = $settings['order'];
		}
		if( is_array( $settings['selectedCategories'] ) && count( $settings['selectedCategories'] ) > 0 ){
			$queryArgs['categories'] = $settings['selectedCategories'];
		}
		$products = new \WP_Query( woolentorBlocks_Product_Query( $queryArgs ) );

	
		$content_style = '';
		if( isset( $settings['layout'] ) ){
			if ( $settings['layout'] == '2' ) {
	            $content_style = 'wl_left-item';
	        }elseif ( $settings['layout']=='3' ) {
	            $content_style = 'wl_dark-item';
	        }else{
				$content_style = '';
			}
		}

		ob_start();
		?>
			<div class="<?php echo implode(' ', $areaClasses ); ?>">

				<?php if( $products->have_posts() ): ?>

					<div class="woolentor-row <?php echo ( $settings['noGutter'] === true ? 'wlno-gutters' : '' ); ?>">
						<?php
							while( $products->have_posts() ) {
								$products->the_post();

								$product = wc_get_product( get_the_ID() );

								$btn_class = $product->is_purchasable() && $product->is_in_stock() ? ' add_to_cart_button' : '';

								$btn_class .= $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? ' ajax_add_to_cart' : '';
								$description = wp_trim_words ( get_the_content(), $settings['contentLimit'], '' );

								?>
									<div class="woolentor-col-<?php echo $settings['columns']['desktop']; ?>">
										<div class="wl_single-product-item <?php echo $content_style; ?>">

											<a href="<?php the_permalink(); ?>" class="product-thumbnail">
												<div class="images">
													<?php echo $product->get_image( 'full' ); //woocommerce_template_loop_product_thumbnail(); ?>
												</div>
											</a>

											<div class="product-content">
												<div class="product-content-top">

													<?php if( $settings['showTitle'] === true ): ?>
														<h6 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
													<?php endif; ?>

													<?php if( $settings['showPrice'] === true ): ?>
														<div class="product-price">
															<span class="new-price"><?php woocommerce_template_loop_price();?></span>
														</div>
													<?php endif; ?>

													<?php
														if( $settings['showContent'] === true ){
															echo '<p>'.$description.'</p>';
														}
													?>

													<?php if( $settings['showRating'] === true ): ?>
														<div class="reading">
															<?php woocommerce_template_loop_rating(); ?>
														</div>
													<?php endif; ?>

												</div>
												<ul class="action">
													<li class="wl_cart">
														<a href="<?php echo $product->add_to_cart_url(); ?>" data-quantity="1" class="action-item <?php echo $btn_class; ?>" data-product_id="<?php echo $product->get_id(); ?>"><?php echo __( '<i class="fa fa-shopping-cart"></i>', 'woolentor' );?></a>
													</li>
													<?php
														if( true === woolentor_has_wishlist_plugin() ){
															echo '<li>'.woolentor_add_to_wishlist_button('<i class="fa fa-heart-o"></i>','<i class="fa fa-heart"></i>').'</li>';
														}
													?>                                    
													<?php
														if( function_exists('woolentor_compare_button') && true === woolentor_exist_compare_plugin() ){
															echo '<li>';
																woolentor_compare_button(
																	array(
																		'style' => 2,
																		'btn_text' => '<i class="fas fa-exchange-alt"></i>',
																		'btn_added_txt' => '<i class="fas fa-exchange-alt"></i>' 
																	)
																);
															echo '</li>';
														}
													?>
												</ul>
											</div>

										</div>
									</div>
								<?php
							}
						?>
					</div>

				<?php wp_reset_postdata(); endif; ?>
				
			</div>

		<?php
		return ob_get_clean();
	}

}
WooLentorBlocks_Product_Curvy::instance();