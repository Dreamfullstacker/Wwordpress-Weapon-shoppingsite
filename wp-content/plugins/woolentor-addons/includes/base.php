<?php

namespace WooLentor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Base
*/
final class Base {

    const MINIMUM_PHP_VERSION = '5.4';
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * [$template_info]
     * @var array
     */
    public static $template_info = [];

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Base]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * [__construct] Class construcotr
     */
    private function __construct() {

        if ( ! function_exists('is_plugin_active') ){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }
        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );

        // WooLentor Template CPT Manager
        require( WOOLENTOR_ADDONS_PL_PATH. 'includes/admin/include/class.template-manager.php' );

        // Register Plugin Active Hook
        register_activation_hook( WOOLENTOR_ADDONS_PL_ROOT, [ $this, 'plugin_activate_hook' ] );

        // Register Plugin Deactive Hook
        register_deactivation_hook( WOOLENTOR_ADDONS_PL_ROOT, [ $this, 'plugin_deactivation_hook'] );

        // Support WooCommerce
        add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ] );

    }

    /**
     * [i18n] Load Text Domain
     * @return [void]
     */
    public function i18n() {
        load_plugin_textdomain( 'woolentor', false, dirname( plugin_basename( WOOLENTOR_ADDONS_PL_ROOT ) ) . '/languages/' );
    }

    /**
     * [init] Plugins Loaded Init Hook
     * @return [void]
     */
    public function init() {

        // Check for required PHP version
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        // Check WooCommerce
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action('admin_notices', [ $this, 'admin_notic_missing_woocommerce' ] );
            return;
        }

        // Plugins Setting Page
        add_filter('plugin_action_links_'.WOOLENTOR_PLUGIN_BASE, [ $this, 'plugins_setting_links' ] );

        // Include File
        $this->include_files();

        // After Active Plugin then redirect to setting page
        $this->plugin_redirect_option_page();

        /**
         * [$template_info] Assign template data
         * @var [type]
         */
        if( is_admin() && class_exists('\Woolentor_Template_Library') ){
            self::$template_info = \Woolentor_Template_Library::instance()->get_templates_info();
        }

        // Promo Banner
        if( is_admin() ){
            if( isset( self::$template_info['notices'][0]['status'] ) ){
                if( !is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') && ( self::$template_info['notices'][0]['status'] == 1 ) ){
                    add_action( 'wp_ajax_woolentor_pro_notice', [ $this, 'ajax_dismiss' ] );
                    add_action( 'admin_notices', [ $this, 'admin_promo_notice' ] );
                    return;
                }
            }
        }

        // Elementor Preview Action
        if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
            add_action( 'admin_action_elementor', [ $this, 'wc_fontend_includes' ], 5 );
        }

        // Manage Page Action
        \WooLentor_Page_Action::instance()->init();

    }

    /**
     * [admin_notice_missing_main_plugin] Admin Notice For missing elementor.
     * @return [void]
     */
    public function admin_notice_missing_main_plugin() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $elementor = 'elementor/elementor.php';
        if( $this->is_plugins_active( $elementor ) ) {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
            $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor );
            $message = sprintf( __( '%1$sWooLentor Addons for Elementor%2$s requires %1$s"Elementor"%2$s plugin to be active. Please activate Elementor to continue.', 'woolentor' ), '<strong>', '</strong>' );
            $button_text = esc_html__( 'Activate Elementor', 'woolentor' );
        } else {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
            $activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
            $message = sprintf( __( '%1$sWooLentor Addons for Elementor%2$s requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', 'woolentor' ), '<strong>', '</strong>' );
            $button_text = esc_html__( 'Install Elementor', 'woolentor' );
        }
        $button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
        printf( '<div class="error"><p>%1$s</p>%2$s</div>', $message, $button );
    }

    /**
     * [admin_notic_missing_woocommerce] Admin Notice For missing WooCommerce
     * @return [void]
     */
    public function admin_notic_missing_woocommerce(){
        $woocommerce = 'woocommerce/woocommerce.php';
        if( $this->is_plugins_active( $woocommerce ) ) {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
            $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $woocommerce . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $woocommerce );
            $message = sprintf( __( '%1$sWooLentor Addons for Elementor%2$s requires %1$s"WooCommerce"%2$s plugin to be active. Please activate WooCommerce to continue.', 'woolentor' ), '<strong>', '</strong>');
            $button_text = __( 'Activate WooCommerce', 'woolentor' );
        } else {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
            $activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
            $message = sprintf( __( '%1$sWooLentor Addons for Elementor%2$s requires %1$s"WooCommerce"%2$s plugin to be installed and activated. Please install WooCommerce to continue.', 'woolentor' ), '<strong>', '</strong>' );
            $button_text = __( 'Install WooCommerce', 'woolentor' );
        }
        $button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
        printf( '<div class="error"><p>%1$s</p>%2$s</div>', __( $message ), $button );
    }

    /**
     * [admin_notice_minimum_php_version] Admin Notice For Required PHP Version
     * @return [void]
     */
    public function admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'woolentor' ),
            '<strong>' . esc_html__( 'WooLentor', 'woolentor' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'woolentor' ) . '</strong>',
             self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * [ajax_dismiss] Ajax Call back funtion for update user meta
     * @return [void]
     */
    public function ajax_dismiss() {
        update_user_meta( get_current_user_id(), 'woolentor_dismissed_notice_id', 1 );
        wp_die();
    }

    /**
     * [admin_promo_notice]
     * @return [void] Promo banner admin notice
     */
    public function admin_promo_notice(){

        if( get_user_meta( get_current_user_id(), 'woolentor_dismissed_notice_id', true ) ){
            return;
        }

        if( self::$template_info['notices'] ){
            ?>
            <style type="text/css">
                .woolentor-admin-notice.notice {
                  position: relative;
                  padding-top: 20px !important;
                  padding-right: 40px;
                }
                .woolentor-admin-notice.notice img{
                  width: 100%;
                }
                .woolentor-admin-notice.notice-warning {
                  border-left-color: #22b9ff;
                }
            </style>
            <script>
                ;jQuery( function( $ ) {
                    $( 'div.notice.woolentor-admin-notice' ).on( 'click', 'button.notice-dismiss', function( event ) {
                        event.preventDefault();
                        $.ajax({
                            url: ajaxurl,
                            data: {
                                'action': 'woolentor_pro_notice',
                            }
                        });
                    } );
                });
            </script>
            <?php
            $bannerLink = self::$template_info['notices'][0]['bannerlink'] ? self::$template_info['notices'][0]['bannerlink'] : '#';
            $bannerTitle = self::$template_info['notices'][0]['title'] ? self::$template_info['notices'][0]['title'] : esc_html__('Promo Banner','woolentor');
            $bannerDescription = self::$template_info['notices'][0]['description'] ? '<p>'.self::$template_info['notices'][0]['description'].'</p>' : '';
            $bannerImage = self::$template_info['notices'][0]['bannerimage'] ? '<img src='.self::$template_info['notices'][0]['bannerimage'].' alt='.$bannerTitle.'/>' : '#';

            printf( '<div class="woolentor-admin-notice is-dismissible notice notice-warning"><a href="%1$s" target="_blank">%2$s</a>%3$s</div>', $bannerLink, $bannerImage, $bannerDescription  );
           
        }
    }

   /**
    * [is_plugins_active] Check Plugin is Installed or not
    * @param  [string]  $pl_file_path plugin file path
    * @return boolean  true|false
    */
    public function is_plugins_active( $pl_file_path = NULL ){
        $installed_plugins_list = get_plugins();
        return isset( $installed_plugins_list[$pl_file_path] );
    }

   /**
    * [plugins_setting_links]
    * @param  [array] $links default plugin action link
    * @return [array] plugin action link
    */
    public function plugins_setting_links( $links ) {
        $settings_link = '<a href="'.admin_url('admin.php?page=woolentor').'">'.esc_html__( 'Settings', 'woolentor' ).'</a>'; 
        array_unshift( $links, $settings_link );
        if( !is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') ){
            $links['woolentorgo_pro'] = sprintf('<a href="https://hasthemes.com/plugins/woolentor-pro-woocommerce-page-builder/?fd" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__('Go Pro','woolentor') . '</a>');
        }
        return $links; 
    }

   /**
    * [plugin_activate_hook] Plugin Activation hook callable
    * @return [void]
    */
    public function plugin_activate_hook() {
        add_option( 'woolentor_do_activation_redirect', TRUE );
    }

    /**
     * [plugin_deactivation_hook] Plugin Deactivation hook callable
     * @return [void]
     */
    public function plugin_deactivation_hook() {
        delete_metadata( 'user', null, 'woolentor_dismissed_notice_id', null, true );
    }

    /**
     * [plugin_redirect_option_page] After Active the plugin then redirect to option page
     * @return [void]
     */
    public function plugin_redirect_option_page() {
        if ( get_option( 'woolentor_do_activation_redirect', FALSE ) ) {
            delete_option('woolentor_do_activation_redirect');
            if( !isset( $_GET['activate-multi'] ) ){
                wp_redirect( admin_url("admin.php?page=woolentor") );
            }

            // Fetch Template Library Data
            $transient = get_transient( \Woolentor_Template_Library::TRANSIENT_KEY );
            if ( ! $transient ) {
                $info = \Woolentor_Template_Library::request_remote_templates_info( true );
                set_transient( \Woolentor_Template_Library::TRANSIENT_KEY, $info, DAY_IN_SECONDS );
            }

        }
    }


    /**
     * [after_setup_theme] WooCommerce Support
     * @return [void] 
     */
    public function after_setup_theme() {
        if( function_exists('woolentor_get_option') ){
            if( woolentor_get_option( 'enablecustomlayout', 'woolentor_woo_template_tabs', 'on' ) == 'on' ){
                add_theme_support( 'woocommerce' );
                add_theme_support( 'wc-product-gallery-zoom' );
                add_theme_support( 'wc-product-gallery-lightbox' );
                add_theme_support( 'wc-product-gallery-slider' );
            }
        }
    }

   /**
    * [wc_fontend_includes] Load WC Files in Editor Mode
    * @return [void]
    */
    public function wc_fontend_includes() {
        \WC()->frontend_includes();
        if ( is_null( \WC()->cart ) ) {
            global $woocommerce;
            $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
            $woocommerce->session = new $session_class();
            $woocommerce->session->init();

            $woocommerce->cart     = new \WC_Cart();
            $woocommerce->customer = new \WC_Customer( get_current_user_id(), true );
        }
    }

    /**
     * [include_files] Required File
     * @return [void]
     */
    public function include_files(){

        require( WOOLENTOR_ADDONS_PL_PATH.'includes/helper-function.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.assest_management.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.widgets_control.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.default_data.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.icon-manager.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.quickview_manage.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.icon_list.php' );
        require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.ajax_actions.php' );

        // Admin Setting file
        if( is_admin() ){
            require( WOOLENTOR_ADDONS_PL_PATH.'includes/custom-metabox.php' );
            require( WOOLENTOR_ADDONS_PL_PATH.'includes/admin/admin-init.php' );

            // Post Duplicator
            if( !is_plugin_active('ht-mega-for-elementor/htmega_addons_elementor.php') ){
                if( woolentor_get_option( 'postduplicator', 'woolentor_others_tabs', 'off' ) === 'on' ){
                    require_once ( WOOLENTOR_ADDONS_PL_PATH.'classes/class.post-duplicator.php' );
                }
            }

        }

        // Builder File
        if( woolentor_get_option( 'enablecustomlayout', 'woolentor_woo_template_tabs', 'on' ) == 'on' ){
            require( WOOLENTOR_ADDONS_PL_PATH.'includes/wl_woo_shop.php' );
            require( WOOLENTOR_ADDONS_PL_PATH.'includes/archive_product_render.php' );           
            require( WOOLENTOR_ADDONS_PL_PATH.'includes/class.product_video_gallery.php' );
            if( !is_admin() && woolentor_get_option( 'enablerenamelabel', 'woolentor_rename_label_tabs', 'off' ) == 'on' ){
                require( WOOLENTOR_ADDONS_PL_PATH.'includes/rename_label.php' );
            }
            require( WOOLENTOR_ADDONS_PL_PATH.'classes/class.product_query.php' );
        }

        // Search
        if( woolentor_get_option( 'ajaxsearch', 'woolentor_others_tabs', 'off' ) == 'on' ){
            require( WOOLENTOR_ADDONS_PL_PATH. 'includes/widgets/ajax-search/base.php' );
        }

        // Sale Notification
        if( woolentor_get_option( 'enableresalenotification', 'woolentor_sales_notification_tabs', 'off' ) == 'on' && woolentor_get_option( 'notification_content_type', 'woolentor_sales_notification_tabs', 'actual' ) != 'fakes'){
            
        }

        // Sale Notification
        if( woolentor_get_option( 'enableresalenotification', 'woolentor_sales_notification_tabs', 'off' ) == 'on' ){
            if( woolentor_get_option( 'notification_content_type', 'woolentor_sales_notification_tabs', 'actual' ) == 'fakes' ){
                include( WOOLENTOR_ADDONS_PL_PATH. 'includes/class.sale_notification_fake.php' );
            }else{
                require( WOOLENTOR_ADDONS_PL_PATH. 'includes/class.sale_notification.php' );
            }
        }

        // Single Product Ajax cart
        if( woolentor_get_option( 'ajaxcart_singleproduct', 'woolentor_others_tabs', 'off' ) == 'on' ){
            if ( 'yes' === get_option('woocommerce_enable_ajax_add_to_cart') ) {
                require( WOOLENTOR_ADDONS_PL_PATH. 'classes/class.single_product_ajax_add_to_cart.php' );
            }
        }

        // Page Action
        require( WOOLENTOR_ADDONS_PL_PATH. 'classes/class.page_action.php' );

        // Modules Manager
        require( WOOLENTOR_ADDONS_PL_PATH. 'includes/modules/class.module-manager.php' );


    }
    

}

/**
 * Initializes the main plugin
 *
 * @return \Base
 */
function woolentor() {
    return Base::instance();
}