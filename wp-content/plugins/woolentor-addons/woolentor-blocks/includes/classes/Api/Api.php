<?php
namespace WooLentorBlocks\Api;

use Exception;
use WP_REST_Server;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load general WP action hook
 */
class Api {

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->namespace = 'woolentor/v1';
	}

	/**
	 * Resgister routes
	 */
	public function register_routes() {

        register_rest_route(  $this->namespace, 'category', 
            [
                'methods' => WP_REST_Server::READABLE,
                'args' => [
                    'querySlug'  => [],
                    'queryLimit' => [],
                    'queryOrder' => [],
                    'queryType'  => [],
                    'wpnonce'    => []
                ],
                'callback'            => [ $this, 'get_category_data' ],
                'permission_callback' => [ $this, 'permission_check' ],
            ]
        );

        register_rest_route( $this->namespace, 'products', 
            [
                'methods' => WP_REST_Server::READABLE,
                'args' => [
                    'perPage'  => [],
                    'categories' => [], 
                    'orderBy'  => [], 
                    'order'    => [],
                    'filterBy' => [],
                    'offset'   => [], 
                    'include'  => [], 
                    'exclude'  => [],
                    'wpnonce'  => []
                ],
                'callback' => [ $this, 'get_post_data' ],
                'permission_callback' => '__return_true'
            ]
        );

        register_rest_route( $this->namespace, 'imagesizes', 
            [
                'methods' => WP_REST_Server::READABLE,
                'args' => [
                    'wpnonce'  => []
                ],
                'callback' => [ $this, 'get_image_sizes' ],
                'permission_callback' => '__return_true'
            ]
        );

	}

    /**
     * Api permission check
     */
    public function permission_check() {
        if( current_user_can( 'edit_posts' ) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get category data
     */
    public function get_category_data( $request ){
        
        if ( !wp_verify_nonce( $_REQUEST['wpnonce'], 'woolentorblock-nonce') ){
            return rest_ensure_response([]);
        }

        $data = woolentorBlocks_taxnomy_data( $request['querySlug'], $request['queryLimit'], $request['queryOrder'], $request['queryType'] );
        return rest_ensure_response( $data );

    }

    /**
     * Get Image sizes data
     */
    public function get_image_sizes( $request ){
        
        if ( !wp_verify_nonce( $_REQUEST['wpnonce'], 'woolentorblock-nonce') ){
            return rest_ensure_response([]);
        }

        $data = woolentorBlocks_get_image_size();
        return rest_ensure_response( $data );

    }

    /**
     * Get Post data
     */
    public function get_post_data( $request ){

        if ( !wp_verify_nonce( $_REQUEST['wpnonce'], 'woolentorblock-nonce') ){
            return rest_ensure_response([]);
        }

        $data = [];
        $loop = new \WP_Query( woolentorBlocks_Product_Query( $request ) );

        if( $loop->have_posts() ){
            while( $loop->have_posts() ) {
                $loop->the_post();
                
                $item                   = array();
                $product_id             = get_the_ID();
                $product                = wc_get_product( $product_id );
                $user_id                = get_the_author_meta('ID');
                $item['id']             = $product_id;
                $item['time']           = get_the_date();
                $item['title']          = get_the_title();
                $item['permalink']      = get_permalink();
                $item['excerpt']        = strip_tags( get_the_excerpt() );
                $item['content']        = strip_tags( get_the_content() );
                $item['price_sale']     = $product->get_sale_price();
                $item['price_regular']  = $product->get_regular_price();
                $item['on_sale']        = $product->is_on_sale();
                $item['discount']       = ( $item['price_sale'] && $item['price_regular'] ) ? round( ( $item['price_regular'] - $item['price_sale'] ) / $item['price_regular'] * 100 ).'%' : '';
                $item['price_html']     = $product->get_price_html();
                $item['stock']          = $product->get_stock_status();
                $item['featured']       = $product->is_featured();
                $item['rating']         = [
                    'count'             => $product->get_rating_count(),
                    'average'           => $product->get_average_rating(),
                    'html'              => wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ),
                ];
                $cart_btn_class = $product->is_purchasable() && $product->is_in_stock() ? ' add_to_cart_button' : '';
                $cart_btn_class .= $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? ' ajax_add_to_cart' : '';
                $item['addtocart']      = [
                    'link'      => $product->add_to_cart_url(),
                    'class'     => $cart_btn_class,
                ];
                $item['wishlist']       = [
                    'status' => woolentor_has_wishlist_plugin(),
                    'html'   => woolentor_add_to_wishlist_button()
                ];
                $item['compare']       = [
                    'status' => woolentor_exist_compare_plugin(),
                    'html'   => woolentorBlocks_compare_button( array( 'style'=>2 ) ),
                ];

                $time           = current_time('timestamp');
		        $time_to        = strtotime( $product->get_date_on_sale_to() );
                $item['deal']   = ( $item['price_sale'] && $time_to > $time ) ? date( 'Y/m/d', $time_to ) : '';

                // Images
                if( has_post_thumbnail() ){
                    $thumb_id       = get_post_thumbnail_id( $product_id );
                    $image_sizes    = woolentorBlocks_get_image_size();
                    $image_src      = array();
                    foreach ( $image_sizes as $key => $size ) {
                        $image_src[$key] = [
                            'src' => wp_get_attachment_image_src( $thumb_id, $key, false )[0],
                            'html' => $product->get_image( $image_size )
                        ];
                    }
                    $item['image'] = $image_src;
                }

                // Tags
                $tags = get_the_terms( $product_id, ( isset( $prams['tag'] ) ? esc_attr( $prams['tag'] ) : 'product_tag' ) );
                if( !empty( $tag ) ){
                    $tag_list = array();
                    foreach ( $tags as $tag ) {
                        $tag_list[] = [
                            'slug' => $tag->slug, 
                            'name' => $tag->name, 
                            'url' => get_term_link( $tag->term_id )
                        ];
                    }
                    $item['tags'] = $tag_list;
                }

                // Categories
                $categories = get_the_terms( $product_id, ( isset( $prams['cat'] ) ? esc_attr( $prams['cat'] ) : 'product_cat') );
                if( !empty( $categories ) ){
                    $category_list = array();
                    foreach ( $categories as $category ) {
                        $category_list[] = [ 
                            'slug' => $category->slug, 
                            'name' => $category->name, 
                            'url' => get_term_link( $category->term_id )
                        ];
                    }
                    $item['categories'] = $category_list;
                }
                $data[] = $item;
            }
            wp_reset_postdata();
        }
        return rest_ensure_response( $data );

    }

	
}
