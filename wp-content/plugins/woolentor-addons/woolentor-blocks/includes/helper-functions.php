<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit();

// Generate CSS
function woolentorBlocks_generate_css( $settings, $attribute, $css_attr, $unit = '', $important = '' ){

    $value = !empty( $settings[$attribute] ) ? $settings[$attribute] : '';

    if( !empty( $value ) && 'NaN' !== $value ){
        $css_attr .= ":{$value}{$unit}";
        return $css_attr."{$important};";
    }else{
        return false;
    }

}

// Geterante Dimension
function woolentorBlocks_Dimention_Control( $settings, $attribute, $css_attr, $important = '' ){
    $dimensions = !empty( $settings[$attribute] ) ? $settings[$attribute] : array();

    if( isset( $dimensions['top'] ) || isset( $dimensions['right'] ) || isset( $dimensions['bottom'] ) || isset( $dimensions['left'] ) ){
        $unit = empty( $dimensions['unit'] ) ? 'px' : $dimensions['unit'];

        $top = ( $dimensions['top'] !== '' ) ? $dimensions['top'].$unit : null;
        $right = ( $dimensions['right'] !== '' ) ? $dimensions['right'].$unit : null;
        $bottom = ( $dimensions['bottom'] !== '' ) ? $dimensions['bottom'].$unit : null;
        $left = ( $dimensions['left'] !== '' ) ? $dimensions['left'].$unit : null;
        $css_dimension = ( ($top != null) || ($right !=null) || ($bottom != null) || ($left != '') ) ? ( $css_attr.":{$top} {$right} {$bottom} {$left};" ) : '';

        return $css_dimension."{$important};";

    }else{
        return false;
    }

}

// Background Image control
function woolentorBlocks_Background_Control( $settings, $attribute ){
    $background_property = !empty( $settings[$attribute] ) ? $settings[$attribute] : array();
    
    if( !empty( $background_property['imageId'] ) ){
        $image_url = wp_get_attachment_image_src( $background_property['imageId'], 'full' );
        $background_css = "background-image:url({$image_url[0]});";

        if( !empty( $background_property['position'] ) ){
            $background_css .= "background-position:{$background_property['position']};";
        }
        if( !empty( $background_property['attachment'] ) ){
            $background_css .= "background-attachment:{$background_property['attachment']};";
        }
        if( !empty( $background_property['repeat'] ) ){
            $background_css .= "background-repeat:{$background_property['repeat']};";
        }
        if( !empty( $background_property['size'] ) ){
            $background_css .= "background-size:{$background_property['size']};";
        }

        return  $background_css;

    }else{
        return false;
    }
    
}

/**
 * Check Gutenberg editor page
 */
function woolentorBlocks_is_gutenberg_page() {

    if ( !function_exists( 'get_current_screen' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    }
    
	// Gutenberg plugin is enable.
    if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) { 
        return true;
    }
	
	// Gutenberg editor page
	$current_screen = get_current_screen();
	if ( $current_screen !== NULL && method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
        return true;
	}
	
    return false;

}

/**
 * current page blocks
 */
function woolentorBlocks_check_inner_blocks( $block ) {
    static $currentBlocks = [];
    
    $current = $block;

    if( $block['blockName'] == 'core/block' ) { //reusable block
        $current = parse_blocks( get_post_field( 'post_content', $block['attrs']['ref'] ) )[0];
    }

    if( $current['blockName'] != '' ) {
        array_push( $currentBlocks, $current );
        if( count( $current['innerBlocks'] ) > 0 ){
            foreach( $current['innerBlocks'] as $innerBlock ) {
                woolentorBlocks_check_inner_blocks( $innerBlock );
            }
        }
    }
    return $currentBlocks;
}

/**
 * Get All Current page blocks
 */
function woolentorBlocks_get_blocks(){
    $get_blocks = [];

    $posts_array = get_post();
    if( $posts_array ){
        foreach( parse_blocks( $posts_array->post_content ) as $block){
            $get_blocks = woolentorBlocks_check_inner_blocks( $block );
        }
    }

    return $get_blocks;

}

/**
 * Get Image Sizes
 */
function woolentorBlocks_get_image_size() {
    $sizes = get_intermediate_image_sizes();
    $filter = array('full' => 'Full');
    foreach ( $sizes as $value ) {
        $filter[$value] = ucwords( str_replace( array('_', '-'), array(' ', ' '), $value ) );
    }
    return $filter;
}

/**
 * Get Category data
 */
function woolentorBlocks_taxnomy_data( $taxnomySlug = '', $number = 20, $order = 'asc', $type = '' ){
    
    $data = array();
    $taxnomyKey = 'product_cat';

    $queryArg = array(
        'orderby'    => 'name',
        'order'      => $order,
        'number'     => $number,
        'hide_empty' => true,
    );

    if( !empty( $taxnomySlug ) ){
        $queryArg['slug'] = $taxnomySlug;
    }

    $term_data = get_terms( 'product_cat', $queryArg );

    if( !empty( $term_data ) && !is_wp_error( $term_data ) ){

        foreach ( $term_data as $terms ) {
            $tempData = array();
            $thumbnail_id   = get_term_meta( $terms->term_id, 'thumbnail_id', true ) ? get_term_meta( $terms->term_id, 'thumbnail_id', true ) : ''; 
            $tempData['link']   = get_term_link( $terms );
            $tempData['name']   = $terms->name;
            $tempData['slug']   = $terms->slug;
            $tempData['desc']   = $terms->description;
            $tempData['count']  = $terms->count;
            $tempData['thumbnail_id']  = $thumbnail_id ? $thumbnail_id : '';
            $tempData['placeholderImg']  = wc_placeholder_img_src( 'woocommerce_single' );
            
            // Images
            if( $thumbnail_id ){
                $image_sizes    = woolentorBlocks_get_image_size();
                $image_src      = array();
                foreach ( $image_sizes as $key => $size ) {
                    $image_src[$key] = [
                        'src' => wp_get_attachment_image_src( $thumbnail_id, $key, false )[0],
                        'html' => wp_get_attachment_image( $thumbnail_id, $key )
                    ];
                }
                $tempData['image'] = $image_src;
            }

            $data[] = $tempData;
        }
        
    }

    return $data;

}

function woolentorBlocks_Product_Query( $params ){
    
    $meta_query = $tax_query = array();
    
    $query_args = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'posts_per_page'    => isset( $params['perPage'] ) ? $params['perPage'] : 4,
        'order'             => isset( $params['order'] ) ? $params['order'] : 'DESC',
        'orderby'           => isset( $params['orderBy'] ) ? $params['orderBy'] : 'date',
        'paged'             => isset( $params['paged'] ) ? $params['paged'] : 1,
    );

    // Categories wise
    if( isset( $params['categories'] ) ){
        $field_name = 'slug';
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'terms' => $params['categories'],
            'field' => $field_name,
            'include_children' => false
        );
    }

    // Tag wise
    if( isset( $params['tags'] ) ){
        $field_name = 'slug';
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'terms' => $params['tags'],
            'field' => $field_name,
            'include_children' => false
        );
    }
    $query_args['tax_query'] = $tax_query;

    if( isset( $params['offset'] ) && $params['offset'] && !( $query_args['paged'] > 1 ) ){
        $query_args['offset'] = isset( $params['offset'] ) ? $params['offset'] : 0;
    }

    if( isset( $params['include'] ) && $params['include'] ){
        $query_args['post__in'] = explode( ',', $params['include'] );
    }

    if( isset( $params['exclude'] ) && $params['exclude'] ){
        $query_args['post__not_in'] = explode( ',', $params['exclude'] );
    }

    if( isset( $params['filterBy'] ) ){

        switch ( $params['filterBy'] ) {
            
            case 'featured':
                $query_args['post__in'] = wc_get_featured_product_ids();
            break;
    
            case 'best_selling':
                $query_args['meta_key']   = 'total_sales';
                $query_args['orderby']    = 'meta_value_num';
                $query_args['order']      = 'desc';
            break;

            case 'sale':
                $query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
            break;
    
            case 'top_rated': 
                $query_args['meta_key']   = '_wc_average_rating';
                $query_args['orderby']    = 'meta_value_num';
                $query_args['order']      = 'desc';          
            break;
    
            case 'mixed_order':
                $query_args['orderby']    = 'rand';
            break;
    
            default: /* Recent */
                $query_args['orderby']    = 'date';
                $query_args['order']      = 'desc';
            break;
            
        }

    }

    /**
     * Custom Order
     */
    if( isset( $params['orderBy'] ) && 'none' != $params['orderBy'] ){
        $query_args['orderby'] = $params['orderBy'];
    }
    if( isset( $params['order'] ) ){
        $query_args['order'] = $params['order'];
    }

    $query_args['wpnonce'] = wp_create_nonce( 'woolentorblock-nonce' );

    return $query_args;
}

function woolentorBlocks_compare_button( $button_arg = array() ){

    global $product;
    $product_id = $product->get_id();

    $output = '';

    $button_style       = !empty( $button_arg['style'] ) ? $button_arg['style'] : 1;

    if( class_exists('Ever_Compare') ){

        $button_title       = !empty( $button_arg['title'] ) ? $button_arg['title'] : esc_html__('Add to Compare','woolentor');
        $button_text        = !empty( $button_arg['btn_text'] ) ? $button_arg['btn_text'] : esc_html__('Add to Compare','woolentor');
        $button_added_text  = !empty( $button_arg['btn_added_txt'] ) ? $button_arg['btn_added_txt'] : esc_html__( 'Product Added','woolentor' );

        $comp_link = \EverCompare\Frontend\Manage_Compare::instance()->get_compare_page_url();
        $output = '<a title="'.esc_attr( $button_title ).'" href="'.esc_url( $comp_link ).'" class="htcompare-btn woolentor-compare" data-added-text="'.esc_attr( $button_added_text ).'" data-product_id="'.esc_attr( $product_id ).'">'.$button_text.'</a>';
        return $output;

    }elseif( class_exists('YITH_Woocompare') ){
        $comp_link = home_url() . '?action=yith-woocompare-add-product';
        $comp_link = add_query_arg('id', $product_id, $comp_link);

        if( $button_style == 1 ){
            if( class_exists('YITH_Woocompare_Frontend') ){
                $output = do_shortcode('[yith_compare_button]');
            }
        }else{
            $output = '<a title="'. esc_attr__('Add to Compare', 'woolentor') .'" href="'. esc_url( $comp_link ) .'" class="woolentor-compare compare" data-product_id="'. esc_attr( $product_id ) .'" rel="nofollow">'.esc_html__( 'Compare', 'woolentor' ).'</a>';
        }
        return $output;
    }else{
        return 0;
    }

}