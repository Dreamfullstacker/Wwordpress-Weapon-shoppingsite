<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooLentorBlocks_Product_Tab{
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
		include WOOLENTOR_BLOCK_PATH . '/src/blocks/product-tab/block.json';
		$metadata = json_decode( ob_get_clean(), true );

		register_block_type(
			$metadata['name'],
			array(
				'attributes'  => $metadata['attributes'],
				'render_callback' => [ $this,'product_tab_render' ],
				'script'          => 'slick',
			)

			// array(
			// 	'attributes'  => $metadata['attributes'],
			// 	'render_callback' => [ $this,'product_tab_render' ],
			// 	'editor_script'   => 'slick',
			// 	'editor_style'    => 'woolentor-widgets',
			// 	'script'          => 'slick',
			// )
		);

	}

	/**
	 * Product tab callable function
	 */
	public function product_tab_render( $settings, $content ){

		$product_style 	= $settings['style'];
		$columns 		= $settings['columns'];
		$rows 			= $settings['rows'];
		$customClass 	= !empty( $settings['className'] ) ? $settings['className'] : '';
		$proslider 		= $settings['slider'] ? 'yes' : 'no';
		$producttab 	= $settings['productTab'] ? 'yes' : 'no';

		$product_type 	= $settings['productFilterType'];
		$per_page 		= $settings['perPage'];
		$custom_order 	= $settings['customOrder'];

		$query_args = array(
			'per_page' => $per_page,
			'product_type' => $product_type
		);

		// Category Wise
        $product_cats = !empty( $settings['selectedCategories'] ) ? $settings['selectedCategories'] : array();
        if( is_array( $product_cats ) && count( $product_cats ) > 0 ){
            $query_args['categories'] = $product_cats;
        }

		// Custom Order
        if( $custom_order == true ){
			$orderby = $settings['orderBy'];
			$order 	 = $settings['order'];
            $query_args['custom_order'] = array (
                'orderby' => $orderby,
                'order' => $order,
            );
        }

		$args = woolentor_product_query( $query_args );

		$products = new \WP_Query( $args );

		// Slider Options
		$slider_settings = array();
		if( $proslider == 'yes' ){
			$is_rtl = is_rtl();
			$direction = $is_rtl ? 'rtl' : 'ltr';
			$slider_settings = [
				'arrows' => (true === $settings['slarrows']),
				'dots' => (true === $settings['sldots']),
				'autoplay' => (true === $settings['slautolay']),
				'autoplay_speed' => absint($settings['slautoplaySpeed']),
				'animation_speed' => absint($settings['slanimationSpeed']),
				'pause_on_hover' => ('yes' === $settings['slpauseOnHover']),
				'rtl' => $is_rtl,
			];

			$slider_responsive_settings = [
				'product_items' => $settings['slitems'],
				'scroll_columns' => $settings['slscrollItem'],
				'tablet_width' => $settings['sltabletWidth'],
				'tablet_display_columns' => $settings['sltabletDisplayColumns'],
				'tablet_scroll_columns' => $settings['sltabletScrollColumns'],
				'mobile_width' => $settings['slMobileWidth'],
				'mobile_display_columns' => $settings['slMobileDisplayColumns'],
				'mobile_scroll_columns' => $settings['slMobileScrollColumns'],

			];
			$slider_settings = array_merge( $slider_settings, $slider_responsive_settings );
		}

		if( ( $proslider == 'yes' ) && ( $producttab != 'yes' ) ){
			$collumval = 'slide-item woolentor-col-1';
		}else{
			$collumval = !empty( $settings['columns'] ) ? 'woolentor-col-'.$settings['columns'] : 'woolentor-col-4';
		}

		$tabuniqid = $settings['blockUniqId'];
		$uniqClass = 'woolentorblock-'.$settings['blockUniqId'];
		$customClass .= ' '.$uniqClass;
		!empty( $settings['className'] ) ? $customClass .= ' '.$settings['className'] : '';

		ob_start();
		?>
		<div class="<?php echo $customClass; ?>">
			<div class="woolentor-product-tab-area <?php echo !empty( $settings['columns'] ) ? 'woolentor-columns-'.$settings['columns'] : 'woolentor-columns-1'; ?>">

			<?php if ( $producttab == 'yes' ) { ?>
                <div class="product-tab-list ht-text-center">
                    <ul class="ht-tab-menus">
                        <?php
                            $m=0;
                            if( is_array( $product_cats ) && count( $product_cats ) > 0 ){

                                // Category retrive
                                $catargs = array(
                                    'orderby'    => 'name',
                                    'order'      => 'ASC',
                                    'hide_empty' => true,
                                    'slug'       => $product_cats,
                                );
                                $prod_categories = get_terms( 'product_cat', $catargs);

                                foreach( $prod_categories as $prod_cats ){
                                    $m++;
                                    $field_name = is_numeric( $product_cats[0] ) ? 'term_id' : 'slug';
                                    $args['tax_query'] = array(
                                        array(
                                            'taxonomy' => 'product_cat',
                                            'terms' => $prod_cats,
                                            'field' => $field_name,
                                            'include_children' => false
                                        ),
                                    );
                                    if( 'featured' == $product_type ){
                                        $args['tax_query'][] = array(
                                            'taxonomy' => 'product_visibility',
                                            'field'    => 'name',
                                            'terms'    => 'featured',
                                            'operator' => 'IN',
                                        );
                                    }
                                    $fetchproduct = new \WP_Query( $args );

                                    if( $fetchproduct->have_posts() ){
                                        ?>
                                            <li><a class="<?php if($m==1){ echo 'htactive';}?>" href="#woolentortab<?php echo $tabuniqid.esc_attr($m);?>">
                                                <?php echo esc_attr( $prod_cats->name,'woolentor' );?>
                                            </a></li>
                                        <?php
                                    }
                                }
                            }
                        ?>
                    </ul>
                </div>
            <?php }; ?>

			<?php if( is_array( $product_cats ) && (count( $product_cats ) > 0) && ( $producttab == 'yes' ) ): ?>

				<?php
					$j=0;
					$tabcatargs = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'slug'       => $product_cats,
					);
					$tabcat_fach = get_terms( 'product_cat', $tabcatargs );
					foreach( $tabcat_fach as $cats ):
						$j++;
						$field_name = is_numeric($product_cats[0])?'term_id':'slug';
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'product_cat',
								'terms' => $cats,
								'field' => $field_name,
								'include_children' => false
							)
						);
						if( 'featured' == $product_type ){
							$args['tax_query'][] = array(
								'taxonomy' => 'product_visibility',
								'field'    => 'name',
								'terms'    => 'featured',
								'operator' => 'IN',
							);
						}
						$products = new \WP_Query( $args );

						if( $products->have_posts() ):
				?>
					<div class="ht-tab-pane <?php if( $j==1 ){ echo 'htactive'; } ?>" id="<?php echo 'woolentortab'.$tabuniqid.$j;?>">
						<div class="woolentor-row">

							<!-- product item start -->
							<div class="<?php echo esc_attr( $collumval );?>">
							<?php
								$loopitem = 1;
								while( $products->have_posts() ): $products->the_post();

								$this->render_item( $settings, $loopitem );

								if( $loopitem % $rows == 0 && ($products->post_count != $loopitem ) ){
									echo '</div><div class="'.esc_attr( $collumval ).'">';
								}
								$loopitem++; endwhile; wp_reset_query(); wp_reset_postdata();
								echo '</div>';
							?>
							<!-- product item end -->

						</div>
					</div>
                <?php endif; endforeach;?>

			<?php else:?>
				<div class="woolentor-row">

					<?php if( $proslider == 'yes' ){ echo '<div id="product-slider-' . $settings['blockUniqId'] . '" dir="'.$direction.'" class="product-slider" data-settings=\'' . wp_json_encode($slider_settings) . '\'>';}?>
						
						<!-- product item start -->
						<div class="<?php echo esc_attr( $collumval );?>">
						<?php
							$loopitem = 1;
							while( $products->have_posts() ): $products->the_post();

							$this->render_item( $settings, $loopitem );

							if( $loopitem % $rows == 0 && ($products->post_count != $loopitem ) ){
								echo '</div><div class="'.esc_attr( $collumval ).'">';
							}
							$loopitem++; endwhile; wp_reset_query(); wp_reset_postdata();
							echo '</div>';
						?>
						<!-- product item end -->

					<?php if( $proslider == 'yes' ){ echo '</div>';} ?>

				</div>
			<?php endif;?>

			</div>

		</div>
		<?php
		return ob_get_clean();

	}

	/**
	 * Tab item
	 */
	public function render_item( $settings, $loopitem ){

		$rows = $settings['rows'];

		?>
		<div class="product-item <?php if ( $rows > 1 && ( $loopitem % $rows != 0 ) ){ echo 'mb-30 ';} if( $settings['style'] == 3){ echo 'product_style_three'; }?> ">

			<div class="product-inner">
				<div class="image-wrap">
					<a href="<?php the_permalink();?>" class="image">
						<?php 
							woocommerce_show_product_loop_sale_flash();
							woocommerce_template_loop_product_thumbnail();
						?>
					</a>
					<?php
						if( $settings['style'] == 1){
							if( true === woolentor_has_wishlist_plugin() ){
								echo woolentor_add_to_wishlist_button();
							}
						}
					?>
					<?php if( $settings['style'] == 3):?>
						<div class="product_information_area">

							<?php
								global $product; 
								$attributes = $product->get_attributes();
								if($attributes):
									echo '<div class="product_attribute">';
									foreach ( $attributes as $attribute ) :
										$name = $attribute->get_name();
									?>
									<ul>
										<?php
											echo '<li class="attribute_label">'.wc_attribute_label( $attribute->get_name() ).esc_html__(':','woolentor').'</li>';
											if ( $attribute->is_taxonomy() ) {
												global $wc_product_attributes;
												$product_terms = wc_get_product_terms( $product->get_id(), $name, array( 'fields' => 'all' ) );
												foreach ( $product_terms as $product_term ) {
													$product_term_name = esc_html( $product_term->name );
													$link = get_term_link( $product_term->term_id, $name );
													$color = get_term_meta( $product_term->term_id, 'color', true );
													if ( ! empty ( $wc_product_attributes[ $name ]->attribute_public ) ) {
														echo '<li><a href="' . esc_url( $link  ) . '" rel="tag">' . $product_term_name . '</a></li>';
													} else {
														if(!empty($color)){
															echo '<li class="color_attribute" style="background-color: '.$color.';">&nbsp;</li>';
														}else{
															echo '<li>' . $product_term_name . '</li>';
														}
														
													}
												}
											}
										?>
									</ul>
							<?php endforeach; echo '</div>'; endif;?>

							<div class="actions style_two">
								<?php
									woocommerce_template_loop_add_to_cart();
									if( true === woolentor_has_wishlist_plugin() ){
										echo woolentor_add_to_wishlist_button();
									}
								?>
							</div>

							<div class="content">
								<h4 class="title"><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h4>
								<?php woocommerce_template_loop_price();?>
							</div>

						</div>

					<?php else:?>
						<div class="actions <?php if( $settings['style'] == 2){ echo 'style_two'; }?>">
							<?php
								if( $settings['style'] == 2){
									woocommerce_template_loop_add_to_cart();
									if( true === woolentor_has_wishlist_plugin() ){
										echo woolentor_add_to_wishlist_button();
									}
								}else{
									woocommerce_template_loop_add_to_cart(); 

									if( function_exists('woolentor_compare_button') && true === woolentor_exist_compare_plugin() ){
										woolentor_compare_button();
									}

								}
							?>
						</div>
					<?php endif;?>

					
				</div>
				
				<div class="content">
					<h4 class="title"><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h4>
					<?php woocommerce_template_loop_price();?>
				</div>
			</div>

		</div>
		<?php

	}

}
WooLentorBlocks_Product_Tab::instance();