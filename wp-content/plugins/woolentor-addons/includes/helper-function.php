<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit();

/**
* Elementor Version check
* Return boolean value
*/
function woolentor_is_elementor_version( $operator = '<', $version = '2.6.0' ) {
    return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, $version, $operator );
}

/**
 * [movepro_render_icon]
 * @param  array  $settings 
 * @param  string $new_icon  new icon id
 * @param  string $old_icon  Old icon id
 * @param  array  $attributes icon attributes
 * @return [html]  html | false
 */
function woolentor_render_icon( $settings = [], $new_icon = 'selected_icon', $old_icon = 'icon', $attributes = [] ){

    $migrated = isset( $settings['__fa4_migrated'][$new_icon] );
    $is_new = empty( $settings[$old_icon] ) && \Elementor\Icons_Manager::is_migration_allowed();

    $attributes['aria-hidden'] = 'true';
    $output = '';

    if ( woolentor_is_elementor_version( '>=', '2.6.0' ) && ( $is_new || $migrated ) ) {

        if ( empty( $settings[$new_icon]['library'] ) ) {
            return false;
        }

        $tag = 'i';
        // handler SVG Icon
        if ( 'svg' === $settings[$new_icon]['library'] ) {
            if ( ! isset( $settings[$new_icon]['value']['id'] ) ) {
                return '';
            }
            $output = Elementor\Core\Files\Assets\Svg\Svg_Handler::get_inline_svg( $settings[$new_icon]['value']['id'] );

        } else {
            $icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
            if ( isset( $icon_types[ $settings[$new_icon]['library'] ]['render_callback'] ) && is_callable( $icon_types[ $settings[$new_icon]['library'] ]['render_callback'] ) ) {
                return call_user_func_array( $icon_types[ $settings[$new_icon]['library'] ]['render_callback'], [ $settings[$new_icon], $attributes, $tag ] );
            }

            if ( empty( $attributes['class'] ) ) {
                $attributes['class'] = $settings[$new_icon]['value'];
            } else {
                if ( is_array( $attributes['class'] ) ) {
                    $attributes['class'][] = $settings[$new_icon]['value'];
                } else {
                    $attributes['class'] .= ' ' . $settings[$new_icon]['value'];
                }
            }
            $output = '<' . $tag . ' ' . \Elementor\Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
        }

    } else {
        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $settings[ $old_icon ];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $settings[ $old_icon ];
            } else {
                $attributes['class'] .= ' ' . $settings[ $old_icon ];
            }
        }
        $output = sprintf( '<i %s></i>', \Elementor\Utils::render_html_attributes( $attributes ) );
    }

    return $output;
 
}

/**
 * [woolentor_product_query]
 * @param  array  $query_args
 * @return [array] Generate query
 */
function woolentor_product_query( $query_args = [] ){
    
    $meta_query = $tax_query = array();

    $per_page = !empty( $query_args['per_page'] ) ? $query_args['per_page'] : 3;

    // Tex Query
    // 
    // Categories wise
    if( isset( $query_args['categories'] ) ){
        $field_name = 'slug';
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'terms' => $query_args['categories'],
            'field' => $field_name,
            'include_children' => false
        );
    }

    // Tag wise
    if( isset( $query_args['tags'] ) ){
        $field_name = 'slug';
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'terms' => $query_args['tags'],
            'field' => $field_name,
            'include_children' => false
        );
    }

    // Feature Product
    if( $query_args['product_type'] == 'featured' ){
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN',
        );
    }

    // Meta Query
    /**
     * [$hide_out_of_stock] Check ( WooCommerce > Settings > Products > Inventory )
     */
    $hide_out_of_stock = get_option( 'woocommerce_hide_out_of_stock_items', 'no' );
    if( 'yes' === $hide_out_of_stock ){
        $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '==',
        );
    }

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'posts_per_page'        => $per_page,
        'meta_query'            => $meta_query,
        'tax_query'             => $tax_query,
    );

    // Product Type Check
    switch( $query_args['product_type'] ){

        case 'sale':
            $args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
        break;

        case 'best_selling':
            $args['meta_key']   = 'total_sales';
            $args['orderby']    = 'meta_value_num';
            $args['order']      = 'desc';
        break;

        case 'top_rated': 
            $args['meta_key']   = '_wc_average_rating';
            $args['orderby']    = 'meta_value_num';
            $args['order']      = 'desc';          
        break;

        case 'mixed_order':
            $args['orderby']    = 'rand';
        break;

        case 'show_byid':
            $args['post__in'] = $query_args['product_ids'];
            $args['orderby']  = $query_args['product_ids'];
        break;

        case 'show_byid_manually':
            $args['post__in'] = $query_args['product_ids'];
            $args['orderby']  = $query_args['product_ids'];
        break;

        default: /* Recent */
            $args['orderby']    = 'date';
            $args['order']      = 'desc';
        break;

    }

    /**
     * Custom Order
     */
    if( isset( $query_args['custom_order'] ) ){
        $args['orderby'] = $query_args['custom_order']['orderby'];
        $args['order'] = $query_args['custom_order']['order'];
    }

    return $args;

}

/**
 * Get all menu list
 * return array
 */
function woolentor_get_all_create_menus() {
    $raw_menus = wp_get_nav_menus();
    $menus     = wp_list_pluck( $raw_menus, 'name', 'term_id' );
    $parent    = isset( $_GET['parent_menu'] ) ? absint( $_GET['parent_menu'] ) : 0;
    if ( 0 < $parent && isset( $menus[ $parent ] ) ) {
        unset( $menus[ $parent ] );
    }
    return $menus;
}

/**
 *  Taxonomy List
 * @return array
 */
function woolentor_taxonomy_list( $taxonomy = 'product_cat', $option_value = 'slug' ){
    $terms = get_terms( array(
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
    ));
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
        foreach ( $terms as $term ) {
            $options[ $term->$option_value ] = $term->name;
        }
        return $options;
    }
}

/*
 * Get Post Type
 * return array
 */
function woolentor_get_post_types( $args = [] ) {
    $post_type_args = [
        'show_in_nav_menus' => true,
    ];
    if ( ! empty( $args['post_type'] ) ) {
        $post_type_args['name'] = $args['post_type'];
    }
    $_post_types = get_post_types( $post_type_args , 'objects' );

    $post_types  = [];
    if( !empty( $args['defaultadd'] ) ){
        $post_types[ strtolower($args['defaultadd']) ] = ucfirst($args['defaultadd']);
    }
    foreach ( $_post_types as $post_type => $object ) {
        $post_types[ $post_type ] = $object->label;
    }
    return $post_types;
}


/**
 * Get Post List
 * return array
 */
function woolentor_post_name( $post_type = 'post' ){
    $options = array();
    $options['0'] = __('Select','woolentor');
    $perpage = woolentor_get_option( 'loadproductlimit', 'woolentor_others_tabs', '20' );
    $all_post = array( 'posts_per_page' => $perpage, 'post_type'=> $post_type );
    $post_terms = get_posts( $all_post );
    if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ){
        foreach ( $post_terms as $term ) {
            $options[ $term->ID ] = $term->post_title;
        }
        return $options;
    }
}

/*
 * Elementor Templates List
 * return array
 */
function woolentor_elementor_template() {
    $templates = '';
    if( class_exists('\Elementor\Plugin') ){
        $templates = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' )->get_items();
    }
    $types = array();
    if ( empty( $templates ) ) {
        $template_lists = [ '0' => __( 'No saved templates found.', 'woolentor' ) ];
    } else {
        $template_lists = [ '0' => __( 'Select Template', 'woolentor' ) ];
        foreach ( $templates as $template ) {
            $template_lists[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
        }
    }
    return $template_lists;
}

/*
 * Woolentor Templates List
 * return array
 */
function woolentor_wltemplate_list( $type = [] ){
    $template_lists = [];

    $args = array(
        'post_type'            => 'woolentor-template',
        'post_status'          => 'publish',
        'ignore_sticky_posts'  => 1,
        'posts_per_page'       => -1,
    );

    if( is_array( $type ) && count( $type ) > 0 ){
        $args['meta_key'] = 'woolentor_template_meta_type';
        $args['meta_value'] = $type;
        $args['meta_compare'] = 'IN';
    }

    $templates = new WP_Query( $args );

    if( $templates->have_posts() ){
        foreach ( $templates->get_posts() as $post ) {
            $template_lists[ $post->ID ] = $post->post_title;
        }
    }
    wp_reset_query();
    return $template_lists;

}

/*
 * Plugisn Options value
 * return on/off
 */
function woolentor_get_option( $option, $section, $default = '' ){
    $options = get_option( $section );
    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
    return $default;
}

function woolentor_get_option_label_text( $option, $section, $default = '' ){
    $options = get_option( $section );
    if ( isset( $options[$option] ) ) {
        if( !empty($options[$option]) ){
            return $options[$option];
        }
        return $default;
    }
    return $default;
}

/**
 * [woolentor_update_option]
 * @param  [string] $option
 * @param  [string] $section
 * @param  string $new_value
 * @return [string]
 */
function woolentor_update_option( $section, $option_key, $new_value ){
    $options_data = get_option( $section );
    if( isset( $options_data[$option_key] ) ){
        $options_data[$option_key] = $new_value;
    }else{
        $options_data = array( $option_key => $new_value );
    }
    update_option( $section, $options_data );
}

/**
 * [woolentor_clean]
 * @param  [JSON] $var
 * @return [array]
 */
function woolentor_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'woolentor_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

/**
 * Call a shortcode function by tag name.
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function woolentor_do_shortcode( $tag, array $atts = array(), $content = null ) {
    global $shortcode_tags;

    if ( ! isset( $shortcode_tags[ $tag ] ) ) {
        return false;
    }

    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/**
* Woocommerce Product last product id return
*/
function woolentor_get_last_product_id(){
    global $wpdb;
    
    // Getting last Product ID (max value)
    $results = $wpdb->get_col( "
        SELECT MAX(ID) FROM {$wpdb->prefix}posts
        WHERE post_type LIKE 'product'
        AND post_status = 'publish'" 
    );
    return reset($results);
}

/*
 * HTML Tag list
 * return array
 */
function woolentor_html_tag_lists() {
    $html_tag_list = [
        'h1'   => __( 'H1', 'woolentor' ),
        'h2'   => __( 'H2', 'woolentor' ),
        'h3'   => __( 'H3', 'woolentor' ),
        'h4'   => __( 'H4', 'woolentor' ),
        'h5'   => __( 'H5', 'woolentor' ),
        'h6'   => __( 'H6', 'woolentor' ),
        'p'    => __( 'p', 'woolentor' ),
        'div'  => __( 'div', 'woolentor' ),
        'span' => __( 'span', 'woolentor' ),
    ];
    return $html_tag_list;
}

/*
 * HTML Tag Validation
 * return strig
 */
function woolentor_validate_html_tag( $tag ) {
    $allowed_html_tags = [
        'article',
        'aside',
        'footer',
        'header',
        'section',
        'nav',
        'main',
        'div',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'p',
        'span',
    ];
    return in_array( strtolower( $tag ), $allowed_html_tags ) ? $tag : 'div';
}

/* 
* Category list
* return first one
*/
function woolentor_get_product_category_list( $id = null, $taxonomy = 'product_cat', $limit = 1 ) { 
    $terms = get_the_terms( $id, $taxonomy );
    $i = 0;
    if ( is_wp_error( $terms ) )
        return $terms;

    if ( empty( $terms ) )
        return false;

    foreach ( $terms as $term ) {
        $i++;
        $link = get_term_link( $term, $taxonomy );
        if ( is_wp_error( $link ) ) {
            return $link;
        }
        echo '<a href="' . esc_url( $link ) . '">' . $term->name . '</a>';
        if( $i == $limit ){
            break;
        }else{ continue; }
    }
    
}

/*
* If Active WooCommerce
*/
if( class_exists('WooCommerce') ){

    /* Custom product badge */
    function woolentor_custom_product_badge( $show = 'yes' ){
        global $product;
        $custom_saleflash_text = get_post_meta( get_the_ID(), '_saleflash_text', true );
        if( $show == 'yes' ){
            if( !empty( $custom_saleflash_text ) && $product->is_in_stock() ){
                if( $product->is_featured() ){
                    echo '<span class="ht-product-label ht-product-label-left hot">' . esc_html( $custom_saleflash_text ) . '</span>';
                }else{
                    echo '<span class="ht-product-label ht-product-label-left">' . esc_html( $custom_saleflash_text ) . '</span>';
                }
            }
        }
    }

    /* Sale Flash for Single Product page */
    function woolentor_show_product_sale_flash(){
        global $post, $product;
        if( is_a( $product, 'WC_Product' ) ){
            if( $product->is_on_sale() && $product->is_in_stock() ){
                echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woolentor' ) . '</span>', $post, $product );
            }else{
                $out_of_stock = get_post_meta( get_the_ID(), '_stock_status', true );
                $out_of_stock_text = apply_filters( 'woolentor_shop_out_of_stock_text', __( 'Out of stock', 'woolentor' ) );
                if ( 'outofstock' === $out_of_stock ) {
                    echo '<span class="outofstock onsale">'.esc_html( $out_of_stock_text ).'</span>';
                }
            }
        }
    }

    /* Sale badge */
    function woolentor_sale_flash( $offertype = 'default' ){
        global $product;
        if( $product->is_on_sale() && $product->is_in_stock() ){
            if( $offertype !='default' && $product->get_regular_price() > 0 ){
                $_off_percent = (1 - round($product->get_price() / $product->get_regular_price(), 2))*100;
                $_off_price = round($product->get_regular_price() - $product->get_price(), 0);
                $_price_symbol = get_woocommerce_currency_symbol();
                $symbol_pos = get_option('woocommerce_currency_pos', 'left');
                $price_display = '';
                switch( $symbol_pos ){
                    case 'left':
                        $price_display = '-'.$_price_symbol.$_off_price;
                    break;
                    case 'right':
                        $price_display = '-'.$_off_price.$_price_symbol;
                    break;
                    case 'left_space':
                        $price_display = '-'.$_price_symbol.' '.$_off_price;
                    break;
                    default: /* right_space */
                        $price_display = '-'.$_off_price.' '.$_price_symbol;
                    break;
                }
                if( $offertype == 'number' ){
                    echo '<span class="ht-product-label ht-product-label-right">'.$price_display.'</span>';
                }elseif( $offertype == 'percent'){
                    echo '<span class="ht-product-label ht-product-label-right">'.$_off_percent.'%</span>';
                }else{ echo ' '; }

            }else{
                $sale_badge_text = apply_filters( 'woolentor_sale_badge_text', __( 'Sale!', 'woolentor' ) );
                echo '<span class="ht-product-label ht-product-label-right">'.esc_html( $sale_badge_text ).'</span>';
            }
        }else{
            $out_of_stock = get_post_meta( get_the_ID(), '_stock_status', true );
            $out_of_stock_text = apply_filters( 'woolentor_shop_out_of_stock_text', __( 'Out of stock', 'woolentor' ) );
            if ( 'outofstock' === $out_of_stock ) {
                echo '<span class="ht-stockout ht-product-label ht-product-label-right">'.esc_html( $out_of_stock_text ).'</span>';
            }
        }

    }

    // Shop page header result count
    function woolentor_product_result_count( $total, $perpage, $paged ){
        wc_set_loop_prop( 'total', $total );
        wc_set_loop_prop( 'per_page', $perpage );
        wc_set_loop_prop( 'current_page', $paged );
        $geargs = array(
            'total'    => wc_get_loop_prop( 'total' ),
            'per_page' => wc_get_loop_prop( 'per_page' ),
            'current'  => wc_get_loop_prop( 'current_page' ),
        );
        wc_get_template( 'loop/result-count.php', $geargs );
    }

    // product shorting
    function woolentor_product_shorting( $getorderby ){
        ?>
        <div class="woolentor-custom-sorting">
            <form class="woocommerce-ordering" method="get">
                <select name="orderby" class="orderby">
                    <?php
                        $catalog_orderby = apply_filters( 'woocommerce_catalog_orderby', array(
                            'menu_order' => __( 'Default sorting', 'woolentor' ),
                            'popularity' => __( 'Sort by popularity', 'woolentor' ),
                            'rating'     => __( 'Sort by average rating', 'woolentor' ),
                            'date'       => __( 'Sort by latest', 'woolentor' ),
                            'price'      => __( 'Sort by price: low to high', 'woolentor' ),
                            'price-desc' => __( 'Sort by price: high to low', 'woolentor' ),
                        ) );
                        foreach ( $catalog_orderby as $id => $name ){
                            echo '<option value="' . esc_attr( $id ) . '" ' . selected( $getorderby, $id, false ) . '>' . esc_attr( $name ) . '</option>';
                        }
                    ?>
                </select>
                <?php
                    // Keep query string vars intact
                    foreach ( $_GET as $key => $val ) {
                        if ( 'orderby' === $key || 'submit' === $key )
                            continue;
                        if ( is_array( $val ) ) {
                            foreach( $val as $innerVal ) {
                                echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
                            }
                        } else {
                            echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
                        }
                    }
                ?>
            </form>
        </div>
        <?php
    }

    // Custom page pagination
    function woolentor_custom_pagination( $totalpage ){
        echo '<div class="ht-row woocommerce"><div class="ht-col-xs-12"><nav class="woocommerce-pagination">';
            echo paginate_links( apply_filters(
                    'woocommerce_pagination_args', array(
                        'base'=> esc_url( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ), 
                        'format'    => '', 
                        'current'   => max( 1, get_query_var( 'paged' ) ), 
                        'total'     => $totalpage, 
                        'prev_text' => '&larr;', 
                        'next_text' => '&rarr;', 
                        'type'      => 'list', 
                        'end_size'  => 3, 
                        'mid_size'  => 3 
                    )
                )       
            );
        echo '</div></div></div>';
    }

    // Change Product Per page
    if( woolentor_get_option( 'enablecustomlayout', 'woolentor_woo_template_tabs', 'on' ) == 'on' ){
        function woolentor_custom_number_of_posts() {
            $limit = woolentor_get_option( 'shoppageproductlimit', 'woolentor_woo_template_tabs', 2 );
            $postsperpage = apply_filters( 'product_custom_limit', $limit );
            return $postsperpage;
        }
        add_filter( 'loop_shop_per_page', 'woolentor_custom_number_of_posts' );
    }

    // Customize rating html
    if( !function_exists('woolentor_wc_get_rating_html') ){
        function woolentor_wc_get_rating_html(){
            if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) { return; }
            global $product;
            $rating_count = $product->get_rating_count();
            $average      = $product->get_average_rating();
            $rating_whole = floor($average);
            $rating_fraction = $average - $rating_whole;
            $flug = 0;   
            
            if ( $rating_count > 0 ) {
                $wrapper_class = is_single() ? 'rating-number' : 'top-rated-rating';
                ob_start();
            ?>
                <div class="<?php echo esc_attr( $wrapper_class ); ?>">
                    <span class="ht-product-ratting">
                        <span class="ht-product-user-ratting">
                            <?php for($i = 1; $i <= 5; $i++){
                                if( $i <= $rating_whole ){
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    if( $rating_fraction > 0 && $flug == 0 ){
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                        $flug = 1;
                                    } else {
                                        echo '<i class="far fa-star empty"></i>';
                                    }
                                }
                            } ?>
                        </span>
                    </span>
                </div>
                 <?php
                    $html = ob_get_clean();
                } else {
                    $html  = '';
                }

                return $html;
        }
    }

    // HTML Markup Render in footer
    function woolentor_html_render_infooter(){
        do_action( 'woolentor_footer_render_content' );
    }
    add_action( 'wp_footer', 'woolentor_html_render_infooter' );

    /**
     * [woolentor_stock_status]
     */
    function woolentor_stock_status( $order_text, $available_text, $product_id ){

        if ( get_post_meta( $product_id, '_manage_stock', true ) == 'yes' ) {

            $total_stock = get_post_meta( $product_id, 'woolentor_total_stock_quantity', true );

            if ( ! $total_stock ) { echo '<div class="stock-management-progressbar">'.__( 'Set the initial stock amount from', 'woolentor' ).' <a href="'.get_edit_post_link( $product_id ).'" target="_blank">'.__( 'here', 'woolentor' ).'</a></div>'; return; }

            $current_stock = round( get_post_meta( $product_id, '_stock', true ) );

            $total_sold = $total_stock > $current_stock ? $total_stock - $current_stock : 0;
            $percentage = $total_sold > 0 ? round( $total_sold / $total_stock * 100 ) : 0;

            if ( $current_stock > 0 ) {
                echo '<div class="woolentor-stock-progress-bar">';
                    echo '<div class="wlstock-info">';
                        echo '<div class="wltotal-sold">' . __( $order_text, 'woolentor' ) . '<span>' . esc_html( $total_sold ) . '</span></div>';
                        echo '<div class="wlcurrent-stock">' . __( $available_text, 'woolentor' ) . '<span>' . esc_html( $current_stock ) . '</span></div>';
                    echo '</div>';
                    echo '<div class="wlprogress-area" title="' . __( 'Sold', 'woolentor' ) . ' ' . esc_attr( $percentage ) . '%">';
                        echo '<div class="wlprogress-bar"style="width:' . esc_attr( $percentage ) . '%;"></div>';
                    echo '</div>';
                echo '</div>';
            }else{
                echo '<div class="stock-management-progressbar">'.__( 'Set the initial stock amount from', 'woolentor' ).' <a href="'.get_edit_post_link( $product_id ).'" target="_blank">'.__( 'here', 'woolentor' ).'</a></div>';
            }

        }

    }

    /**
     * [woolentor_minmax_price_limit]
     * @return [array] Price Limit
     */
    function woolentor_minmax_price_limit() {
        global $wpdb;
        $min_query = "SELECT MIN( CAST( meta_value as UNSIGNED ) ) FROM {$wpdb->postmeta} WHERE meta_key = '_price'";
        $max_query = "SELECT MAX( CAST( meta_value as UNSIGNED ) ) FROM {$wpdb->postmeta} WHERE meta_key = '_price'";
        $value_min = $wpdb->get_var( $min_query );
        $value_max = $wpdb->get_var( $max_query );
        return [
            'min' => (int)$value_min,
            'max' => (int)$value_max,
        ];
    }

}

/**
 * [woolentor_pro_get_taxonomies]
 * @return [array] product texonomies
 */
function woolentor_get_taxonomies( $object = 'product' ) {
    $all_taxonomies = get_object_taxonomies( $object );
    $taxonomies_list = [];
    foreach ( $all_taxonomies as $taxonomy_data ) {
        $taxonomy = get_taxonomy( $taxonomy_data );
        if( $taxonomy->show_ui ) {
            $taxonomies_list[ $taxonomy_data ] = $taxonomy->label;
        }
    }
    return $taxonomies_list;
}

/**
 * [woolentor_order_by_opts]
 * @return [array] [description]
 */
function woolentor_order_by_opts() {
    $options = [
        'none'                  => esc_html__( 'None', 'woolentor' ),
        'ID'                    => esc_html__( 'ID', 'woolentor' ),
        'date'                  => esc_html__( 'Date', 'woolentor' ),
        'name'                  => esc_html__( 'Name', 'woolentor' ),
        'title'                 => esc_html__( 'Title', 'woolentor' ),
        'comment_count'         => esc_html__( 'Comment count', 'woolentor' ),
        'rand'                  => esc_html__( 'Random', 'woolentor' ),
        'featured'              => esc_html__( 'Featured', 'woolentor' ),
        '_price'                => esc_html__( 'Product Price', 'woolentor' ),
        'total_sales'           => esc_html__( 'Top Seller', 'woolentor' ),
        '_wc_average_rating'    => esc_html__( 'Top Rated', 'woolentor' ),
    ];
    return apply_filters( 'woolentor_order_by_opts', $options );

}

/**
 * [woolentor_exist_compare_plugin]
 * @return [bool]
 */
function woolentor_exist_compare_plugin(){
    if( class_exists('Ever_Compare') ){
        return true;
    }elseif( class_exists('YITH_Woocompare') ){
        return true;
    }else{
        return false;
    }
}

/**
* Usages: Compare button shortcode [yith_compare_button] From "YITH WooCommerce Compare" plugins.
* Plugins URL: https://wordpress.org/plugins/yith-woocommerce-compare/
* File Path: yith-woocommerce-compare/includes/class.yith-woocompare-frontend.php
* The Function "woolentor_compare_button" Depends on YITH WooCommerce Compare plugins. If YITH WooCommerce Compare is installed and actived, then it will work.
*/
function woolentor_compare_button( $button_arg = array() ){

    global $product;
    $product_id = $product->get_id();

    $button_style       = !empty( $button_arg['style'] ) ? $button_arg['style'] : 1;
    
    $button_title       = !empty( $button_arg['title'] ) ? $button_arg['title'] : esc_html__('Add to Compare','woolentor');
    $button_text        = !empty( $button_arg['btn_text'] ) ? $button_arg['btn_text'] : esc_html__('Add to Compare','woolentor');
    $button_added_text  = !empty( $button_arg['btn_added_txt'] ) ? $button_arg['btn_added_txt'] : esc_html__( 'Product Added','woolentor' );

    if( class_exists('Ever_Compare') ){
        $comp_link = \EverCompare\Frontend\Manage_Compare::instance()->get_compare_page_url();
        echo '<a title="'.esc_attr( $button_title ).'" href="'.esc_url( $comp_link ).'" class="htcompare-btn woolentor-compare" data-added-text="'.esc_attr( $button_added_text ).'" data-product_id="'.esc_attr( $product_id ).'">'.$button_text.'</a>';

    }elseif( class_exists('YITH_Woocompare') ){
        $comp_link = home_url() . '?action=yith-woocompare-add-product';
        $comp_link = add_query_arg('id', $product_id, $comp_link);

        if( $button_style == 1 ){
            if( class_exists('YITH_Woocompare_Frontend') ){
                echo do_shortcode('[yith_compare_button]');
            }
        }else{
            echo '<a title="'. esc_attr__('Add to Compare', 'woolentor') .'" href="'. esc_url( $comp_link ) .'" class="woolentor-compare compare" data-product_id="'. esc_attr( $product_id ) .'" rel="nofollow">'.esc_html__( 'Compare', 'woolentor' ).'</a>';
        }
    }else{
        return 0;
    }

}



/**
 * [woolentor_has_wishlist_plugin]
 * @return [bool]
 */
function woolentor_has_wishlist_plugin(){
    if( class_exists('WishSuite_Base') ){
        return true;
    }elseif( class_exists('YITH_WCWL') ){
        return true;
    }elseif( class_exists('TInvWL_Public_AddToWishlist') ){
        return true;
    }else{
        return false;
    }
}

/**
* Usages: "woolentor_add_to_wishlist_button()" function is used  to modify the wishlist button from "YITH WooCommerce Wishlist" plugins.
* Plugins URL: https://wordpress.org/plugins/yith-woocommerce-wishlist/
* File Path: yith-woocommerce-wishlist/templates/add-to-wishlist.php
* The below Function depends on YITH WooCommerce Wishlist plugins. If YITH WooCommerce Wishlist is installed and actived, then it will work.
*/

function woolentor_add_to_wishlist_button( $normalicon = '<i class="fa fa-heart-o"></i>', $addedicon = '<i class="fa fa-heart"></i>', $tooltip = 'no' ) {
    global $product;

    $product_id = $product->get_id();

    $output = '';

    if( class_exists('WishSuite_Base') ){

        $button_text        = wishsuite_get_option( 'button_text','wishsuite_settings_tabs', 'Wishlist' );
        $button_added_text  = wishsuite_get_option( 'added_button_text','wishsuite_settings_tabs', 'Product Added' );
        $button_exist_text  = wishsuite_get_option( 'exist_button_text','wishsuite_settings_tabs', 'Product already added' );
        
        $button_text = $normalicon.'<span class="wishsuite-btn-text">'.$button_text.'</span>';
        $button_added_text = $addedicon.'<span class="wishsuite-btn-text">'.$button_added_text.'</span>';
        $button_exist_text = $addedicon.'<span class="wishsuite-btn-text">'.$button_exist_text.'</span>';
        
        $button_class = 'wishsuite-btn wishsuite-button wishlist'.( $tooltip == 'yes' ? '' : ' wltooltip_no' );

        $button_args = [
            'btn_class' => $button_class,
            'btn_text' => $button_text,
            'btn_added_text' => $button_added_text,
            'btn_exist_text' => $button_exist_text,
        ];
        
        add_filter( 'wishsuite_button_arg', function( $button_arg ) use ( $button_args ) {

            $button_arg['button_class'] = $button_args['btn_class'];

            return $button_arg;
        }, 90, 1 );

        $output .= do_shortcode('[wishsuite_button]');
        return $output;

    }elseif( class_exists('TInvWL_Public_AddToWishlist') ){
        ob_start();
        TInvWL_Public_AddToWishlist::instance()->htmloutput();
        $output .= ob_get_clean();
        return $output;

    }elseif( class_exists( 'YITH_WCWL' ) ){

        if( !empty( get_option( 'yith_wcwl_wishlist_page_id' ) ) ){
            global $yith_wcwl;
            $url          = YITH_WCWL()->get_wishlist_url();
            $product_type = $product->get_type();
            $exists       = $yith_wcwl->is_product_in_wishlist( $product->get_id() );
            $classes      = 'class="add_to_wishlist"';
            $add          = get_option( 'yith_wcwl_add_to_wishlist_text' );
            $browse       = get_option( 'yith_wcwl_browse_wishlist_text' );
            $added        = get_option( 'yith_wcwl_product_added_text' );

            $output  .= '<div class="'.( $tooltip == 'yes' ? '' : 'tooltip_no' ).' wishlist button-default yith-wcwl-add-to-wishlist add-to-wishlist-' . esc_attr( $product->get_id() ) . '">';
                $output .= '<div class="yith-wcwl-add-button';
                    $output .= $exists ? ' hide" style="display:none;"' : ' show"';
                    $output .= '><a href="' . esc_url( htmlspecialchars( YITH_WCWL()->get_wishlist_url() ) ) . '" data-product-id="' . esc_attr( $product->get_id() ) . '" data-product-type="' . esc_attr( $product_type ) . '" ' . $classes . ' >'.$normalicon.'<span class="ht-product-action-tooltip">'.esc_html( $add ).'</span></a>';
                    $output .= '<i class="fa fa-spinner fa-pulse ajax-loading" style="visibility:hidden"></i>';
                $output .= '</div>';

                $output .= '<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><a class="" href="' . esc_url( $url ) . '">'.$addedicon.'<span class="ht-product-action-tooltip">'.esc_html( $browse ).'</span></a></div>';
                $output .= '<div class="yith-wcwl-wishlistexistsbrowse ' . ( $exists ? 'show' : 'hide' ) . '" style="display:' . ( $exists ? 'block' : 'none' ) . '"><a href="' . esc_url( $url ) . '" class="">'.$addedicon.'<span class="ht-product-action-tooltip">'.esc_html( $added ).'</span></a></div>';
            $output .= '</div>';

            return $output;
        }

    }else{
        return 0;
    }


}

/*
* Ajax login Action
*/
global $user;
if ( empty( $user->ID ) ) {
    add_action('init', 'woolentor_ajax_login_init' );
}

function woolentor_ajax_login_init() {
    add_action( 'wp_ajax_nopriv_woolentor_ajax_login', 'woolentor_ajax_login' );
}

/*
 * ajax login
 */
function woolentor_ajax_login(){
    
    // $message = WC_Form_Handler::process_login();

    $all_notices = wc_print_notices( true );

    wp_send_json_success(
        array(
            'notices' => $all_notices,
        )
    );

    wp_die();

}