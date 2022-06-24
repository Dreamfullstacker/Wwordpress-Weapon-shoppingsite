<?php

//Exit if accessed directly
if(!defined('ABSPATH')){
	return;
}


//Quick View Template.
function xoo_qv_ajax(){
	$product_id = (int) $_POST['product_id'];
	$qv_next 	= (int) $_POST['qv_next'];
	$qv_prev 	= (int) $_POST['qv_prev'];

	$params = array('p' => $product_id,
					'post_type' => array('product','product_variation'));
	$query = new WP_Query($params);
	if($query->have_posts()){
		while ($query->have_posts()){
			$query->the_post();
			require_once (plugin_dir_path(__FILE__) . 'xoo-qv-template.php');

		}
	}
	wp_reset_postdata();
	die();
}
add_action('wp_ajax_xoo_qv_ajax','xoo_qv_ajax');
add_action('wp_ajax_nopriv_xoo_qv_ajax','xoo_qv_ajax');

?>