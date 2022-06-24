<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: 'wc_get_gallery_image_html' was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;


$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

// Meta data of 3D Viewer
$modeview_3d = get_post_meta( get_the_ID(), '_bp3d_product_', true );
$viewer_position = isset($modeview_3d['viewer_position']) ? $modeview_3d['viewer_position'] : '';
?>

<div class="product-modal-wrap">
	<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
		<!-- Custom hook for 3d-viewer -->
		<?php  
		if($viewer_position === 'top') {
			do_action( 'bp3d_product_model_before' ); ?>
				<style>
					.woocommerce div.product div.images .woocommerce-product-gallery__trigger {
						position: absolute;
						top: 385px;
					}
				</style>
			<?php		
		}

		if($viewer_position === 'replace') {
			
		add_filter( 'woocommerce_single_product_image_thumbnail_html',function($content){
			return '';
		}, 10, 2 );
		do_action( 'bp3d_product_model_before' ); 	
		}
		?>

		<figure class="woocommerce-product-gallery__wrapper" style="display:none;">
			<?php

			if ( $post_thumbnail_id ) {
				$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
				$html .= '</div>';
			}

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			do_action( 'woocommerce_product_thumbnails' );
			?>
		</figure>
	</div>
	<?php  
		if( $viewer_position === 'bottom') {
			do_action( 'bp3d_product_model_after' ); 
		}
	?>

</div> <!-- End of Product modal wrap -->



