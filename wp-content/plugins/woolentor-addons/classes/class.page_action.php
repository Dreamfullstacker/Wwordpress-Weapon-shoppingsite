<?php
/**
* WooLentor_Page_Action
*/
class WooLentor_Page_Action{

    /**
     * [$instance]
     * @var null
     */
    private static $instance   = null;

    /**
     * [$product_id]
     * @var null
     */
    private static $product_id = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [WooLentor_Page_Action]
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(){
        $this->checkout_page();
    }

    /*
    * Manage Checkout page action
    */
    public function checkout_page(){
        $template_page_id = woolentor_get_option( 'productcheckoutpage', 'woolentor_woo_template_tabs', '0' );
        if( empty( $template_page_id ) || !is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') ){
            return;
        }

        add_action( 'woocommerce_cart_item_name', [ $this, 'add_product_thumbnail' ], 10, 3 );
        add_action( 'woocommerce_cart_item_class', [ $this, 'add_css_class_in_product_tr' ], 10, 3 );

        add_action( 'woocommerce_checkout_cart_item_quantity', '__return_null', 10, 3 );

        // Quentity Increment / Decrement
        // add_action( 'woocommerce_checkout_cart_item_quantity', [$this, 'quentity_field' ], 10, 3 );
        // if ( !is_user_logged_in() ){
        //     add_action( 'wp_ajax_nopriv_update_order_review', [ $this, 'update_order_review' ] );
        // } else{
        //     add_action( 'wp_ajax_update_order_review', [ $this, 'update_order_review' ] );
        // }

    }

    // Update Order Overview
    public function update_order_review() {
        $data = array();
        parse_str( $_POST['post_data'], $data );

        $cart = $data['cart'];
        foreach ( $cart as $cart_item_key => $cart_value ){
            WC()->cart->set_quantity( $cart_key, $cart_value['qty'], false );
            WC()->cart->calculate_totals();
            woocommerce_cart_totals();
        }

        wp_die();
    }

    /*
    * Table Row CSS class add in checkout page order overview table row
    */
    public function add_css_class_in_product_tr( $css_class , $cart_item, $cart_item_key ){
        if ( ! is_checkout() ) return $css_class;
        return  $css_class.' woolentor-order-review-product';
    }

    /*
    * Add Product image to checkout page order overview table
    */
    public function add_product_thumbnail( $product_name, $cart_item, $cart_item_key ){
        if ( ! is_checkout() ) return $product_name;

        $_product = $cart_item['data'];

        //$remove_icon = sprintf( '<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>', esc_url( wc_get_cart_remove_url( $cart_item_key ) ), __( 'Delete', 'woolentor' ), esc_attr( $cart_item['product_id'] ), esc_attr( $_product->get_sku() ));
        
        $thumbnail  =  sprintf('<span class="product-thumbnail">%s</span>', $_product->get_image('thumbnail') );
        $title      = sprintf('<span class="product-title">%s <strong class="product-quantity">&times;&nbsp;%s</strong>%s</span>', $product_name, $cart_item['quantity'], wc_get_formatted_cart_item_data( $cart_item ) );

        return sprintf( '<span class="woolentor-order-item-title">%s %s</span>', $thumbnail, $title );

    }

    // Add Quentity Field in order over view table
    public function quentity_field( $html, $cart_item, $cart_item_key ){

        $_product = $cart_item['data'];

        if ( $_product->is_sold_individually() ) {
            $html = sprintf( ' <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
        } else {
            $html = woocommerce_quantity_input( array(
                'input_name'  => "cart[{$cart_item_key}][qty]",
                'input_value' => $cart_item['quantity'],
                'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                'min_value'   => '1'

                ), $_product, false 
            );
        }

        return $html;
    }


}