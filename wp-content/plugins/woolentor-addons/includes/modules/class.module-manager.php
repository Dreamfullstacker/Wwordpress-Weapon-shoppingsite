<?php  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woolentor_Module_Manager{

    private static $_instance = null;

    /**
     * Instance
     */
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct(){
        $this->include_file();
    }

    /**
     * [include_file] Nessary File Required
     * @return [void]
     */
    public function include_file(){
        
        // Shopify Style Checkout page
        if( woolentor_get_option( 'enable', 'woolentor_shopify_checkout_settings', 'off' ) == 'on' ){
            require( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/shopify-like-checkout/class.shopify-like-checkout.php' );
        }

        // Flash Sale
        if( woolentor_get_option( 'enable', 'woolentor_flash_sale_settings', 'off' ) == 'on' ){
            require( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/flash-sale/class.flash-sale.php' );
        }

        if( is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') ){

            // Partial payment
            if( ( woolentor_get_option( 'enable', 'woolentor_partial_payment_settings', 'off' ) == 'on' ) ){
                require_once( WOOLENTOR_ADDONS_PL_PATH_PRO .'includes/modules/partial-payment/partial-payment.php' );
            }

            // Pre Orders
            if( ( woolentor_get_option( 'enable', 'woolentor_pre_order_settings', 'off' ) == 'on' ) ){
                require_once( WOOLENTOR_ADDONS_PL_PATH_PRO .'includes/modules/pre-orders/pre-orders.php' );
            }

        }
        
    }


}

Woolentor_Module_Manager::instance();