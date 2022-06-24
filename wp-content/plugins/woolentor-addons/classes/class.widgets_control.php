<?php

namespace WooLentor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Widgets Control
*/
class Widgets_Control{
    
    private static $instance = null;
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        $this->init();
    }

    public function init() {

        // Register custom category
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );

        // Init Widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );

    }

    // Add custom category.
    public function add_category( $elements_manager ) {
        
        $elements_manager->add_category(
            'woolentor-addons',
            [
               'title'  => __( 'Woolentor Addons','woolentor'),
                'icon' => 'fa fa-plug',
            ]
        );

        $elements_manager->add_category(
            'woolentor-addons-pro',
            [
               'title'  => __( 'Woolentor Pro','woolentor-pro'),
                'icon' => 'fa fa-plug',
            ]
        );

        // Register In top panel if exist woolentor post type
        if( get_post_type() === 'woolentor-template' ){
            $reorder_cats = function( $categories ){
                uksort( $this->categories, function( $keyOne, $keyTwo ){
                    if( substr( $keyOne, 0, 10 ) == 'woolentor-'){
                        return -1;
                    }
                    if( substr( $keyTwo, 0, 10 ) == 'woolentor-'){
                        return 1;
                    }
                    return 0;
                });

            };
            $reorder_cats->call( $elements_manager, [ 'woolentor-addons', 'woolentor-addons-pro' ] );
        }

    }

    // Widgets Register
    public function init_widgets(){

        if( get_post_type() === 'woolentor-template' ){
            $tmpType  = $this->get_template_type( get_post_meta( get_the_ID(), 'woolentor_template_meta_type', true ) );
        }else{
            $tmpType = '';
        }

        foreach ( $this->widget_list_manager( $tmpType ) as $element_key => $element ){

            $widget_path = ( $element['is_pro'] == true ) ? WOOLENTOR_ADDONS_PL_PATH_PRO : WOOLENTOR_ADDONS_PL_PATH;

            if (  ( woolentor_get_option( $element_key, 'woolentor_elements_tabs', 'on' ) === 'on' ) && file_exists( $widget_path.'includes/addons/'.$element_key.'.php' ) ){
                require_once( $widget_path.'includes/addons/'.$element_key.'.php' );
            }

        }
        
    }

    /* Widget list generate */
    public function widget_list_manager( $tmpType ){

        $is_builder = ( woolentor_get_option( 'enablecustomlayout', 'woolentor_woo_template_tabs', 'on' ) == 'on' ) ? true : false;

        $common_widget  = $this->widget_list()['common'];
        $builder_common = ( $is_builder == true ) ? $this->widget_list()['builder_common'] : [];
        $template_wise  = ( $is_builder == true && $tmpType !== '' && array_key_exists( $tmpType, $this->widget_list() ) ) ? $this->widget_list()[$tmpType] : [];

        $generate_list = [];

        if( $tmpType === '' ){
            foreach( $this->widget_list() as $widget_list_key => $widget_list ){

                if( $is_builder == false ){
                    $generate_list = $common_widget;
                }else{
                    $generate_list += $widget_list;
                }
                
            }
        }else{
            $generate_list = array_merge( $template_wise, $common_widget, $builder_common );
        }

        return $generate_list;

    }

    /* Manage Template type */
    public function get_template_type( $type ){

        switch ( $type ) {

            case 'single':
            case 'quickview':
                $template_type = 'single';
                break;

            case 'shop':
            case 'archive':
                $template_type = 'shop';
                break;

            case 'cart':
                $template_type = 'cart';
                break;

            case 'emptycart':
                $template_type = 'emptycart';
                break;

            case 'minicart':
                $template_type = 'minicart';
                break;

            case 'checkout':
            case 'checkouttop':
                $template_type = 'checkout';
                break;

            case 'myaccount':
            case 'myaccountlogin':
            case 'dashboard':
            case 'orders':
            case 'downloads':
            case 'edit-address':
            case 'edit-account':
                $template_type = 'myaccount';
                break;

            case 'thankyou':
                $template_type = 'thankyou';
                break;

            default:
                $template_type = '';

        }

        return $template_type;

    }

    /* Widget List */
    public function widget_list(){

        $is_pro = is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') ? true : false;

        $widget_list = [
            'common' => [
                'universal_product' => [
                    'title'    => esc_html__('Universal Product','woolentor'),
                    'is_pro'   => $is_pro,
                ],
                'product_tabs' => [
                    'title'    => esc_html__('Product Tabs','woolentor'),
                    'is_pro'   => false,
                ],
                'add_banner' => [
                    'title'     => esc_html__('Adds Banner','woolentor'),
                    'is_pro'    => false,
                ],
                'special_day_offer' => [
                    'title'     => esc_html__('Special Day Offer','woolentor'),
                    'is_pro'    => false,
                ],
                'wb_image_marker' => [
                    'title'     => esc_html__('Image Marker','woolentor'),
                    'is_pro'    => false,
                ],
                'wl_store_features' => [
                    'title'     => esc_html__('Store Features','woolentor'),
                    'is_pro'    => false,
                ],
                'wl_faq' => [
                    'title'     => esc_html__('Faq','woolentor'),
                    'is_pro'    => false,
                ],
                'wl_category_grid' => [
                    'title'     => esc_html__('Category Grid','woolentor'),
                    'is_pro'    => false,
                ],
                'wl_onepage_slider' => [
                    'title'     => esc_html__('One Page Slider','woolentor'),
                    'is_pro'    => false,
                ],
                'product_curvy' => [
                    'title'     => esc_html__('Product Curvy','woolentor'),
                    'is_pro'    => false,
                ],
                'product_image_accordion' => [
                    'title'     => esc_html__('Product Image Accordion','woolentor'),
                    'is_pro'    => false,
                ],
                'product_accordion' => [
                    'title'     => esc_html__('Product Accordion'),
                    'is_pro'    => false,
                ],
                'wl_category' => [
                    'title'    => esc_html__('Category','woolentor'),
                    'is_pro'   => $is_pro,
                ],
                'wl_brand' => [
                    'title'    => esc_html__('Brand','woolentor'),
                    'is_pro'   => $is_pro,
                ],
                'wb_customer_review' => [
                    'title'    => esc_html__('Customer Review','woolentor'),
                    'is_pro'   => $is_pro,
                ],
                'wl_testimonial' => [
                    'title'    => esc_html__('Testimonial','woolentor'),
                    'is_pro'   => $is_pro,
                ],

            ],

            'builder_common' => [

                'wl_product_filter' => [
                    'title'    => esc_html__('Product Filter','woolentor'),
                    'is_pro'   => false,
                ],
                'wl_product_horizontal_filter' => [
                    'title'    => esc_html__('Horizontal Product Filter','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_call_for_price' => [
                    'title'    => esc_html__('Product Call for Price','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_suggest_price' => [
                    'title'    => esc_html__('Product suggest price','woolentor'),
                    'is_pro'   => false,
                ],

            ],

            'single' => [
                'wb_product_title' => [
                    'title'    => esc_html__('Product Title','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_related' => [
                    'title'    => esc_html__('Related Product','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_add_to_cart'=>[
                    'title'    => esc_html__('Product Add To Cart','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_additional_information' => [
                    'title'    => esc_html__('Product Additional Info','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_data_tab' => [
                    'title'    => esc_html__('Product Data tabs','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_description' => [
                    'title'    => esc_html__('Product Description','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_short_description' => [
                    'title'    => esc_html__('Product short description','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_price' => [
                    'title'    => esc_html__('Product Price','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_rating' => [
                    'title'    => esc_html__('Product rating','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_reviews' => [
                    'title'    => esc_html__('Product reviews','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_image' => [
                    'title'    => esc_html__('Product Image','woolentor'),
                    'is_pro'   => false,
                ],
                'wl_product_video_gallery' => [
                    'title'    => esc_html__('Product Video Gallery','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_upsell' => [
                    'title'    => esc_html__('Product Upsell','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_stock' => [
                    'title'    => esc_html__('Product Stock','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_meta' => [
                    'title'    => esc_html__('Product Meta','woolentor'),
                    'is_pro'   => false,
                ],
                'wb_product_qr_code' => [
                    'title'    => esc_html__('Product QR Code','woolentor'),
                    'is_pro'   => false,
                ],
            ],

            'shop' => [
                'wb_archive_product' => [
                    'title'    => esc_html__('Archive Layout Default','woolentor'),
                    'is_pro'   => false,
                ],
            ]

        ];

        if( is_plugin_active('wishsuite/wishsuite.php') ){
            $widget_list['common']['wb_wishsuite_table'] = [
                'title'    => esc_html__('WishSuite Table','woolentor'),
                'is_pro'   => false,
            ];
        }

        if( is_plugin_active('ever-compare/ever-compare.php') ){
            $widget_list['common']['wb_ever_compare_table'] = [
                'title'    => esc_html__('EverCompare','woolentor'),
                'is_pro'   => false,
            ];
        }

        if( is_plugin_active('just-tables/just-tables.php') || is_plugin_active('just-tables-pro/just-tables-pro.php') ){
            $widget_list['common']['wb_just_table'] = [
                'title'    => esc_html__('JustTable','woolentor'),
                'is_pro'   => false,
            ];
        }

        if( is_plugin_active('whols/whols.php') || is_plugin_active('whols-pro/whols-pro.php') ){
            $widget_list['common']['wb_whols'] = [
                'title'    => esc_html__('Whols','woolentor'),
                'is_pro'   => false,
            ];
        }

        if( is_plugin_active('wc-multi-currency/wcmilticurrency.php') || is_plugin_active('multicurrencypro/multicurrencypro.php') ){
            $widget_list['common']['wb_wc_multicurrency'] = [
                'title'    => esc_html__('WC Multicurrency','woolentor'),
                'is_pro'   => false,
            ];
        }

        return apply_filters( 'woolentor_widget_list', $widget_list );


    }


}

Widgets_Control::instance();