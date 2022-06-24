<?php
/**
Plugin Name: Woo Customize Category Template
Description:  Woocomerce customization categories show child category as per selection

**/
add_action("woocommerce_before_shop_loop","woo_sub_category_hooks");
function woo_sub_category_hooks(){
	//echo "Working";
	$queried_object = get_queried_object();
	$term_id = $queried_object->term_id;
	$taxonomy = 'product_cat';

	$children = get_categories( array( 
        'child_of'      => $term_id,
        'taxonomy'      => $taxonomy,
        'hide_empty'    => false,
        'fields'        => 'ids',
    ) );

    /*echo "<pre>";
    print_r($children);*/
    if(!empty($children)){
    	?>
    	<div class="row">
    	<?php
    	foreach($children as $childs){
    		$term = get_term_by( 'id', $childs, $taxonomy);
    		/*echo "<pre>";
    		print_r();*/
    		$thumbnail_id = get_term_meta( $childs, 'thumbnail_id', true ); 

    		// get the image URL
    		$image = wp_get_attachment_url( $thumbnail_id ); 
    		
    		$name = $term->name;
    		//echo "<br>";
    		?>
    		
    		<div class="col-md-4">

    			<div class="product_box category_box">
    				<a href="<?php echo get_term_link($term->slug,$taxonomy); ?>">
    				<div class="product_image category_image">
    				<img src="<?php echo $image; ?>"></div>
    				<h2 class="product_name category_name"><?php echo $name; ?></h2>
    				</a>
    			</div>
    		</div>
    		<?php
    	}
    	?>
    </div>
    	<style type="text/css">
    		.woocommerce-notices-wrapper{
    			display: none;
    		}
    		p.woocommerce-result-count{
    			display: none;
    		}
    		form.woocommerce-ordering{
    			display: none;
    		}
    		ul.products.columns-4{
    			display: none;
    		}
    		nav.woocommerce-pagination{
    			display: none;
    		}
    	</style>
    	<script type="text/javascript">
    		jQuery(document).ready(function(){
    			jQuery("body").addClass("has_subcategory");
    		});
    	</script>
    	<?php
    }
	?>
	<!-- <br><br> -->
	<?php
}
add_filter( 'loop_shop_per_page', 'lw_loop_shop_per_page', 30 );

function lw_loop_shop_per_page( $products ) {
 $products = 12;
 return $products;
}
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}
add_action("woocommerce_before_shop_loop_item_title","woocommerce_before_shop_loop_item_title_products",20);
function woocommerce_before_shop_loop_item_title_products(){
    $prouct_id = get_the_ID();

    ?>
    <!-- <a href="<?php echo do_shortcode('[add_to_cart_url id="'.$prouct_id.'"]'); ?>" class="add_to_cart_btns">Add to cart</a> -->
    <?php
}
add_filter( 'gettext', 'ds_change_readmore_text', 20, 3 );
function ds_change_readmore_text( $translated_text, $text, $domain ) {
if ( ! is_admin() && $domain === 'woocommerce' && $translated_text === 'Read more') {
$translated_text = 'Add to cart';
}
return $translated_text;
}
add_action("woocommerce_after_add_to_cart_button","woocommerce_after_single_variation_funct");
function woocommerce_after_single_variation_funct(){
    echo do_shortcode('[contact-form-7 id="894" title="Out Of Stock"]');
    ?>

    <?php
}
?>