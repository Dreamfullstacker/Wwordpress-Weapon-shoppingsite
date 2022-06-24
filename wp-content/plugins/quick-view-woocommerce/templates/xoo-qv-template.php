<?php

/**
 ========================
   Quick View Template
 ========================
**/

//Exit if accessed directly
if(!defined('ABSPATH')){
	return;
}
?>

<div class="xoo-qv-nxt xooqv-chevron-right xoo-qv" qv-nxt-id ="<?php echo  $qv_next; ?>"></div>
<div class="xoo-qv-prev xooqv-chevron-left xoo-qv" qv-prev-id ="<?php echo  $qv_prev; ?>"></div>

<div class="xoo-qv-inner-modal">
	<div class="xoo-qv-container woocommerce single-product">
		<div class="xoo-qv-top-panel">
			<div class="xoo-qv-close xoo-qv xooqv-cross"></div>
			<div class="xoo-qv-preloader xoo-qv-mpl">
			<div class="xoo-qv-speeding-wheel"></div>
			</div>
		</div>
		<div class="xoo-qv-main" >
			<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class('product'); ?>>
				<div class="xoo-qv-images">
					<?php do_action('xoo-qv-images')?>
				</div>
				<div class="xoo-qv-summary">
					<?php do_action('xoo-qv-summary')?>
				</div>
			</div>
		</div>
	</div>
</div>



