<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woolentor_Admin_Fields {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Woolentor_Admin_Fields]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * [field_sections] Admin Fields section
     * @return [array] section 
     */
    public function field_sections(){

        $sections = array(
            
            array(
                'id'    => 'woolentor_general_tabs',
                'title' => esc_html__( 'General', 'woolentor' ),
                'icon'  => 'wli-cog'
            ),

            array(
                'id'    => 'woolentor_woo_template_tabs',
                'title' => esc_html__( 'WooCommerce Template', 'woolentor' ),
                'icon'  => 'wli-store'
            ),

            array(
                'id'    => 'woolentor_elements_tabs',
                'title' => esc_html__( 'Elements', 'woolentor' ),
                'icon'  => 'wli-images'
            ),

            array(
                'id'    => 'woolentor_others_tabs',
                'title' => esc_html__( 'Modules', 'woolentor' ),
                'icon'  => 'wli-grid'
            ),

            array(
                'id'    => 'woolentor_style_tabs',
                'title' => esc_html__( 'Style', 'woolentor' ),
                'icon'  => 'wli-tag'
            ),

            array(
                'id'    => 'woolentor_extension_tabs',
                'title' => esc_html__( 'Extensions', 'woolentor' ),
                'icon'  => 'wli-masonry'
            ),

        );
        return apply_filters( 'woolentor_admin_fields_sections', $sections );

    }

    /**
     * [fields] Admin Fields
     * @return [array] fields 
     */
    public function fields(){

        $settings_fields = array(

            'woolentor_woo_template_tabs' => array(

                array(
                    'name'    => 'enablecustomlayout',
                    'label'   => esc_html__( 'Enable / Disable Template Builder', 'woolentor' ),
                    'desc'    => esc_html__( 'You can enable/disable template builder from here.', 'woolentor' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),

                array(
                    'name'  => 'shoppageproductlimit',
                    'label' => esc_html__( 'Product Limit', 'woolentor' ),
                    'desc'  => esc_html__( 'You can handle the product limit for the Shop page', 'woolentor' ),
                    'min'               => 1,
                    'max'               => 100,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => '2',
                    'sanitize_callback' => 'floatval',
                    'class' => 'depend_enable_custom_layout',
                ),

                array(
                    'name'    => 'singleproductpage',
                    'label'   => esc_html__( 'Single Product Template', 'woolentor' ),
                    'desc'    => esc_html__( 'You can select a custom template for the product details page layout', 'woolentor' ),
                    'type'    => 'selectgroup',
                    'default' => '0',
                    'options' => [
                        'group'=>[
                            'woolentor' => [
                                'label' => __( 'WooLentor', 'woolentor' ),
                                'options' => woolentor_wltemplate_list( array('single') )
                            ],
                            'elementor' => [
                                'label' => __( 'Elementor', 'woolentor' ),
                                'options' => woolentor_elementor_template()
                            ]
                        ]
                    ],
                    'class'   => 'depend_enable_custom_layout',
                ),

                array(
                    'name'    => 'productarchivepage',
                    'label'   => esc_html__( 'Product Shop Page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'You can select a custom template for the Shop page layout', 'woolentor' ),
                    'type'    => 'selectgroup',
                    'default' => '0',
                    'options' => [
                        'group'=>[
                            'woolentor' => [
                                'label' => __( 'WooLentor', 'woolentor' ),
                                'options' => woolentor_wltemplate_list( array('shop','archive') )
                            ],
                            'elementor' => [
                                'label' => __( 'Elementor', 'woolentor' ),
                                'options' => woolentor_elementor_template()
                            ]
                        ]
                    ],
                    'class'   => 'depend_enable_custom_layout',
                ),

                array(
                    'name'    => 'productallarchivepage',
                    'label'   => esc_html__( 'Product Archive Page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'You can select a custom template for the Product Archive page layout', 'woolentor' ),
                    'type'    => 'selectgroup',
                    'default' => '0',
                    'options' => [
                        'group'=>[
                            'woolentor' => [
                                'label' => __( 'WooLentor', 'woolentor' ),
                                'options' => woolentor_wltemplate_list( array('shop','archive') )
                            ],
                            'elementor' => [
                                'label' => __( 'Elementor', 'woolentor' ),
                                'options' => woolentor_elementor_template()
                            ]
                        ]
                    ],
                    'class'   => 'depend_enable_custom_layout',
                ),

                array(
                    'name'    => 'productcartpagep',
                    'label'   => esc_html__( 'Cart Page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'You can select a template for the Cart page layout', 'woolentor' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => array(
                        'select' => esc_html__('Select a template for the cart page layout','woolentor'),
                    ),
                    'class'   => 'depend_enable_custom_layout',
                    'is_pro'  => true,
                ),

                array(
                    'name'    => 'productcheckoutpagep',
                    'label'   => esc_html__( 'Checkout Page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'You can select a template for the Checkout page layout', 'woolentor' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => array(
                        'select' => esc_html__('Select a template for the Checkout page layout','woolentor'),
                    ),
                    'class'   => 'depend_enable_custom_layout',
                    'is_pro'  => true,
                ),

                array(
                    'name'    => 'productthankyoupagep',
                    'label'   => esc_html__( 'Thank You Page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'Select a template for the Thank you page layout', 'woolentor' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => array(
                        'select' => esc_html__('Select a template for the Thank you page layout','woolentor'),
                    ),
                    'class'     => 'depend_enable_custom_layout',
                    'is_pro'    => true,
                ),

                array(
                    'name'    => 'productmyaccountpagep',
                    'label'   => esc_html__( 'My Account Page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'Select a template for the My Account page layout', 'woolentor' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => array(
                        'select' => esc_html__('Select a template for the My account page layout','woolentor'),
                    ),
                    'class'   => 'depend_enable_custom_layout',
                    'is_pro'  => true,
                ),

                array(
                    'name'    => 'productmyaccountloginpagep',
                    'label'   => esc_html__( 'My Account Login page Template', 'woolentor' ),
                    'desc'    => esc_html__( 'Select a template for the Login page layout', 'woolentor' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => array(
                        'select' => esc_html__('Select a template for the My account login page layout','woolentor'),
                    ),
                    'class'   => 'depend_enable_custom_layout',
                    'is_pro'  => true,
                ),

                array(
                    'name'    => 'productquickviewp',
                    'label'   => esc_html__( 'Quick View Template', 'woolentor' ),
                    'desc'    => esc_html__( 'Select a template for the product\'s quick view layout', 'woolentor' ),
                    'type'    => 'select',
                    'default' => '0',
                    'options' => array(
                        'select' => esc_html__('Select a template for the Quick view layout','woolentor'),
                    ),
                    'class'   => 'depend_enable_custom_layout',
                    'is_pro'  => true,
                ),

            ),

            'woolentor_elements_tabs' => array(

                array(
                    'name'              => 'product_tabs',
                    'label'             => __( 'Product Tab', 'woolentor' ),
                    'type'              => 'element',
                    'default'           => 'on',
                    // 'preview'           => '#',
                    // 'documentation'     => '#',
                    // 'require_settings'  => true,
                    // 'is_pro'            => true
                ),

                array(
                    'name'    => 'universal_product',
                    'label'   => esc_html__( 'Universal Product', 'woolentor' ),
                    'type'    => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'    => 'product_curvy',
                    'label'   => esc_html__( 'WL: Product Curvy', 'woolentor' ),
                    'type'    => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'    => 'product_image_accordion',
                    'label'   => esc_html__( 'WL: Product Image Accordion', 'woolentor' ),
                    'type'    => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'product_accordion',
                    'label' => esc_html__( 'WL: Product Accordion', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'add_banner',
                    'label' => esc_html__( 'Ads Banner', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'special_day_offer',
                    'label' => esc_html__( 'Special Day Offer', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_customer_review',
                    'label' => esc_html__( 'Customer Review', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_image_marker',
                    'label' => esc_html__( 'Image Marker', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_category',
                    'label' => esc_html__( 'Category List', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_category_grid',
                    'label' => esc_html__( 'Category Grid', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_onepage_slider',
                    'label' => esc_html__( 'One page slider', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_testimonial',
                    'label' => esc_html__( 'Testimonial', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_store_features',
                    'label' => esc_html__( 'Store Features', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_faq',
                    'label' => esc_html__( 'Faq', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_brand',
                    'label' => esc_html__( 'Brand Logo', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_archive_product',
                    'label' => esc_html__( 'Product Archive', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_product_filter',
                    'label' => esc_html__( 'Product Filter', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_product_horizontal_filter',
                    'label' => esc_html__( 'Product Horizontal Filter', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_title',
                    'label' => esc_html__( 'Product Title', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_related',
                    'label' => esc_html__( 'Related Product', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_add_to_cart',
                    'label' => esc_html__( 'Add to Cart Button', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_additional_information',
                    'label' => esc_html__( 'Additional Information', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_data_tab',
                    'label' => esc_html__( 'Product Data Tab', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_description',
                    'label' => esc_html__( 'Product Description', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_short_description',
                    'label' => esc_html__( 'Product Short Description', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_price',
                    'label' => esc_html__( 'Product Price', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_rating',
                    'label' => esc_html__( 'Product Rating', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_reviews',
                    'label' => esc_html__( 'Product Reviews', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_image',
                    'label' => esc_html__( 'Product Image', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_product_video_gallery',
                    'label' => esc_html__( 'Product Video Gallery', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_upsell',
                    'label' => esc_html__( 'Product Upsell', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_stock',
                    'label' => esc_html__( 'Product Stock Status', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_meta',
                    'label' => esc_html__( 'Product Meta Info', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_call_for_price',
                    'label' => esc_html__( 'Call for Price', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_suggest_price',
                    'label' => esc_html__( 'Suggest Price', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wb_product_qr_code',
                    'label' => esc_html__( 'QR Code', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'on'
                ),

                array(
                    'name'  => 'wl_product_expanding_gridp',
                    'label' => esc_html__( 'Product Expanding Grid', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_product_filterable_gridp',
                    'label' => esc_html__( 'Product Filterable Grid', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_custom_archive_layoutp',
                    'label' => esc_html__( 'Product Archive Layout', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_product_pgridp',
                    'label' => esc_html__( 'Product Grid', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_cart_tablep',
                    'label' => esc_html__( 'Product Cart Table', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_cart_totalp',
                    'label' => esc_html__( 'Product Cart Total', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_cartempty_messagep',
                    'label' => esc_html__( 'Empty Cart Message', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_cartempty_shopredirectp',
                    'label' => esc_html__( 'Empty Cart Re.. Button', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_cross_sellp',
                    'label' => esc_html__( 'Product Cross Sell', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_cross_sell_customp',
                    'label' => esc_html__( 'Cross Sell ..( Custom )', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_checkout_additional_formp',
                    'label' => esc_html__( 'Checkout Additional..', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_checkout_billingp',
                    'label' => esc_html__( 'Checkout Billing Form', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_checkout_shipping_formp',
                    'label' => esc_html__( 'Checkout Shipping Form', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_checkout_paymentp',
                    'label' => esc_html__( 'Checkout Payment', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_checkout_coupon_formp',
                    'label' => esc_html__( 'Checkout Co.. Form', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_checkout_login_formp',
                    'label' => esc_html__( 'Checkout lo.. Form', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_order_reviewp',
                    'label' => esc_html__( 'Checkout Order Review', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_accountp',
                    'label' => esc_html__( 'My Account', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_navigationp',
                    'label' => esc_html__( 'My Account Navigation', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_dashboardp',
                    'label' => esc_html__( 'My Account Dashboard', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_downloadp',
                    'label' => esc_html__( 'My Account Download', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_edit_accountp',
                    'label' => esc_html__( 'My Account Edit', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_addressp',
                    'label' => esc_html__( 'My Account Address', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_login_formp',
                    'label' => esc_html__( 'Login Form', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_register_formp',
                    'label' => esc_html__( 'Registration Form', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_logoutp',
                    'label' => esc_html__( 'My Account Logout', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_myaccount_orderp',
                    'label' => esc_html__( 'My Account Order', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_thankyou_orderp',
                    'label' => esc_html__( 'Thank You Order', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_thankyou_customer_address_detailsp',
                    'label' => esc_html__( 'Thank You Cus.. Address', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_thankyou_order_detailsp',
                    'label' => esc_html__( 'Thank You Order Details', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_product_advance_thumbnailsp',
                    'label' => esc_html__( 'Advance Product Image', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_product_advance_thumbnails_zoomp',
                    'label' => esc_html__( 'Product Zoom', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_social_sherep',
                    'label' => esc_html__( 'Product Social Share', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_stock_progress_barp',
                    'label' => esc_html__( 'Stock Progress Bar', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),
                array(
                    'name'  => 'wl_single_product_sale_schedulep',
                    'label' => esc_html__( 'Product Sale Schedule', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_related_productp',
                    'label' => esc_html__( 'Related Pro..( Custom )', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_product_upsell_customp',
                    'label' => esc_html__( 'Upsell Pro..( Custom )', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

                array(
                    'name'  => 'wl_mini_cartp',
                    'label' => esc_html__( 'Mini Cart', 'woolentor' ),
                    'type'  => 'element',
                    'default' => 'off',
                    'is_pro' => true,
                ),

            ),

            'woolentor_others_tabs' => array(

                'modules' => array(

                    array(
                        'name'     => 'rename_label_settings',
                        'label'    => esc_html__( 'Rename Label', 'woolentor' ),
                        'type'     => 'module',
                        'default'  => 'off',
                        'section'  => 'woolentor_rename_label_tabs',
                        'option_id'=> 'enablerenamelabel',
                        'require_settings'=> true,
                        'setting_fields' => array(
                            
                            array(
                                'name'  => 'enablerenamelabel',
                                'label' => esc_html__( 'Enable / Disable', 'woolentor' ),
                                'desc'  => esc_html__( 'You can enable / disable rename label from here.', 'woolentor' ),
                                'type'  => 'checkbox',
                                'default' => 'off',
                                'class'   =>'enablerenamelabel woolentor-action-field-left',
                            ),
            
                            array(
                                'name'      => 'shop_page_heading',
                                'headding'  => esc_html__( 'Shop Page', 'woolentor' ),
                                'type'      => 'title',
                                'class'     => 'depend_enable_rename_label',
                            ),
                            
                            array(
                                'name'        => 'wl_shop_add_to_cart_txt',
                                'label'       => esc_html__( 'Add to Cart Button Text', 'woolentor' ),
                                'desc'        => esc_html__( 'Change the Add to Cart button text for the Shop page.', 'woolentor' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Add to Cart', 'woolentor' ),
                                'class'       => 'depend_enable_rename_label woolentor-action-field-left',
                            ),
            
                            array(
                                'name'      => 'product_details_page_heading',
                                'headding'  => esc_html__( 'Product Details Page', 'woolentor' ),
                                'type'      => 'title',
                                'class'     => 'depend_enable_rename_label',
                            ),
            
                            array(
                                'name'        => 'wl_add_to_cart_txt',
                                'label'       => esc_html__( 'Add to Cart Button Text', 'woolentor' ),
                                'desc'        => esc_html__( 'Change the Add to Cart button text for the Product details page.', 'woolentor' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Add to Cart', 'woolentor' ),
                                'class'       => 'depend_enable_rename_label woolentor-action-field-left',
                            ),
            
                            array(
                                'name'        => 'wl_description_tab_menu_title',
                                'label'       => esc_html__( 'Description', 'woolentor' ),
                                'desc'        => esc_html__( 'Change the tab title for the product description.', 'woolentor' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Description', 'woolentor' ),
                                'class'       => 'depend_enable_rename_label woolentor-action-field-left',
                            ),
                            
                            array(
                                'name'        => 'wl_additional_information_tab_menu_title',
                                'label'       => esc_html__( 'Additional Information', 'woolentor' ),
                                'desc'        => esc_html__( 'Change the tab title for the product additional information', 'woolentor' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Additional information', 'woolentor' ),
                                'class'       => 'depend_enable_rename_label woolentor-action-field-left',
                            ),
                            
                            array(
                                'name'        => 'wl_reviews_tab_menu_title',
                                'label'       => esc_html__( 'Reviews', 'woolentor' ),
                                'desc'        => esc_html__( 'Change the tab title for the product review', 'woolentor' ),
                                'type'        => 'text',
                                'placeholder' => __( 'Reviews', 'woolentor' ),
                                'class'       =>'depend_enable_rename_label woolentor-action-field-left',
                            ),
            
                            array(
                                'name'      => 'checkout_page_heading',
                                'headding'  => esc_html__( 'Checkout Page', 'woolentor' ),
                                'type'      => 'title',
                                'class'     => 'depend_enable_rename_label',
                            ),
            
                            array(
                                'name'        => 'wl_checkout_placeorder_btn_txt',
                                'label'       => esc_html__( 'Place order', 'woolentor' ),
                                'desc'        => esc_html__( 'Change the label for the Place order field.', 'woolentor' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Place order', 'woolentor' ),
                                'class'       => 'depend_enable_rename_label woolentor-action-field-left',
                            ),

                        )
                    ),

                    array(
                        'name'     => 'sales_notification_settings',
                        'label'    => esc_html__( 'Sales Notification', 'woolentor' ),
                        'type'     => 'module',
                        'default'  => 'off',
                        'section'  => 'woolentor_sales_notification_tabs',
                        'option_id'=> 'enableresalenotification',
                        'require_settings'  => true,
                        'setting_fields' => array(

                            array(
                                'name'  => 'enableresalenotification',
                                'label' => esc_html__( 'Enable / Disable', 'woolentor' ),
                                'desc'  => esc_html__( 'You can enable / disable sales notification from here.', 'woolentor' ),
                                'type'  => 'checkbox',
                                'default' => 'off',
                                'class' => 'woolentor-action-field-left'
                            ),
                            
                            array(
                                'name'    => 'notification_content_type',
                                'label'   => esc_html__( 'Notification Content Type', 'woolentor' ),
                                'desc'    => esc_html__( 'Select Content Type', 'woolentor' ),
                                'type'    => 'radio',
                                'default' => 'actual',
                                'options' => array(
                                    'actual' => esc_html__('Real','woolentor'),
                                    'fakes'  => esc_html__('Manual','woolentor'),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'noification_fake_data',
                                'label'   => esc_html__( 'Choose Template', 'woolentor' ),
                                'desc'    => esc_html__( 'Choose template for manual notification.', 'woolentor' ),
                                'type'    => 'multiselect',
                                'default' => '',
                                'options' => woolentor_elementor_template(),
                                'class'   => 'notification_fake',
                            ),
            
                            array(
                                'name'    => 'notification_pos',
                                'label'   => esc_html__( 'Position', 'woolentor' ),
                                'desc'    => esc_html__( 'Set the position of the Sales Notification Position on frontend.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => 'bottomleft',
                                'options' => array(
                                    'topleft'       => esc_html__( 'Top Left','woolentor' ),
                                    'topright'      => esc_html__( 'Top Right','woolentor' ),
                                    'bottomleft'    => esc_html__( 'Bottom Left','woolentor' ),
                                    'bottomright'   => esc_html__( 'Bottom Right','woolentor' ),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'notification_layout',
                                'label'   => esc_html__( 'Image Position', 'woolentor' ),
                                'desc'    => esc_html__( 'Set the image position of the notification.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => 'imageleft',
                                'options' => array(
                                    'imageleft'   => esc_html__( 'Image Left','woolentor' ),
                                    'imageright'  => esc_html__( 'Image Right','woolentor' ),
                                ),
                                'class'   => 'notification_real woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'notification_timing_area_title',
                                'headding'=> esc_html__( 'Notification Timing', 'woolentor' ),
                                'type'    => 'title',
                                'size'    => 'margin_0 regular',
                                'class'   => 'element_section_title_area',
                            ),
            
                            array(
                                'name'    => 'notification_loadduration',
                                'label'   => esc_html__( 'First loading time', 'woolentor' ),
                                'desc'    => esc_html__( 'When to start notification load duration.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => '3',
                                'options' => array(
                                    '2'    => esc_html__( '2 seconds','woolentor' ),
                                    '3'    => esc_html__( '3 seconds','woolentor' ),
                                    '4'    => esc_html__( '4 seconds','woolentor' ),
                                    '5'    => esc_html__( '5 seconds','woolentor' ),
                                    '6'    => esc_html__( '6 seconds','woolentor' ),
                                    '7'    => esc_html__( '7 seconds','woolentor' ),
                                    '8'    => esc_html__( '8 seconds','woolentor' ),
                                    '9'    => esc_html__( '9 seconds','woolentor' ),
                                    '10'   => esc_html__( '10 seconds','woolentor' ),
                                    '20'   => esc_html__( '20 seconds','woolentor' ),
                                    '30'   => esc_html__( '30 seconds','woolentor' ),
                                    '40'   => esc_html__( '40 seconds','woolentor' ),
                                    '50'   => esc_html__( '50 seconds','woolentor' ),
                                    '60'   => esc_html__( '1 minute','woolentor' ),
                                    '90'   => esc_html__( '1.5 minutes','woolentor' ),
                                    '120'  => esc_html__( '2 minutes','woolentor' ),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'notification_time_showing',
                                'label'   => esc_html__( 'Notification showing time', 'woolentor' ),
                                'desc'    => esc_html__( 'How long to keep the notification.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => '4',
                                'options' => array(
                                    '2'   => esc_html__( '2 seconds','woolentor' ),
                                    '4'   => esc_html__( '4 seconds','woolentor' ),
                                    '5'   => esc_html__( '5 seconds','woolentor' ),
                                    '6'   => esc_html__( '6 seconds','woolentor' ),
                                    '7'   => esc_html__( '7 seconds','woolentor' ),
                                    '8'   => esc_html__( '8 seconds','woolentor' ),
                                    '9'   => esc_html__( '9 seconds','woolentor' ),
                                    '10'  => esc_html__( '10 seconds','woolentor' ),
                                    '20'  => esc_html__( '20 seconds','woolentor' ),
                                    '30'  => esc_html__( '30 seconds','woolentor' ),
                                    '40'  => esc_html__( '40 seconds','woolentor' ),
                                    '50'  => esc_html__( '50 seconds','woolentor' ),
                                    '60'  => esc_html__( '1 minute','woolentor' ),
                                    '90'  => esc_html__( '1.5 minutes','woolentor' ),
                                    '120' => esc_html__( '2 minutes','woolentor' ),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'notification_time_int',
                                'label'   => esc_html__( 'Time Interval', 'woolentor' ),
                                'desc'    => esc_html__( 'Set the interval time between notifications.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => '4',
                                'options' => array(
                                    '2'   => esc_html__( '2 seconds','woolentor' ),
                                    '4'   => esc_html__( '4 seconds','woolentor' ),
                                    '5'   => esc_html__( '5 seconds','woolentor' ),
                                    '6'   => esc_html__( '6 seconds','woolentor' ),
                                    '7'   => esc_html__( '7 seconds','woolentor' ),
                                    '8'   => esc_html__( '8 seconds','woolentor' ),
                                    '9'   => esc_html__( '9 seconds','woolentor' ),
                                    '10'  => esc_html__( '10 seconds','woolentor' ),
                                    '20'  => esc_html__( '20 seconds','woolentor' ),
                                    '30'  => esc_html__( '30 seconds','woolentor' ),
                                    '40'  => esc_html__( '40 seconds','woolentor' ),
                                    '50'  => esc_html__( '50 seconds','woolentor' ),
                                    '60'  => esc_html__( '1 minute','woolentor' ),
                                    '90'  => esc_html__( '1.5 minutes','woolentor' ),
                                    '120' => esc_html__( '2 minutes','woolentor' ),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'notification_product_display_option_title',
                                'headding'=> esc_html__( 'Product Query Option', 'woolentor' ),
                                'type'    => 'title',
                                'size'    => 'margin_0 regular',
                                'class'   => 'element_section_title_area notification_real',
                            ),
            
                            array(
                                'name'              => 'notification_limit',
                                'label'             => esc_html__( 'Limit', 'woolentor' ),
                                'desc'              => esc_html__( 'Set the number of notifications to display.', 'woolentor' ),
                                'min'               => 1,
                                'max'               => 100,
                                'default'           => '5',
                                'step'              => '1',
                                'type'              => 'number',
                                'sanitize_callback' => 'number',
                                'class'       => 'notification_real woolentor-action-field-left',
                            ),
            
                            array(
                                'name'  => 'showallproduct',
                                'label' => esc_html__( 'Show/Display all products from each order', 'woolentor' ),
                                'desc'  => esc_html__( 'Manage show all product from each order.', 'woolentor' ),
                                'type'  => 'checkbox',
                                'default' => 'off',
                                'class'   => 'notification_real woolentor-action-field-left',
                            ),
            
                            array(
                                'name'    => 'notification_uptodate',
                                'label'   => esc_html__( 'Order Upto', 'woolentor' ),
                                'desc'    => esc_html__( 'Do not show purchases older than.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => '7',
                                'options' => array(
                                    '1'   => esc_html__( '1 day','woolentor' ),
                                    '2'   => esc_html__( '2 days','woolentor' ),
                                    '3'   => esc_html__( '3 days','woolentor' ),
                                    '4'   => esc_html__( '4 days','woolentor' ),
                                    '5'   => esc_html__( '5 days','woolentor' ),
                                    '6'   => esc_html__( '6 days','woolentor' ),
                                    '7'   => esc_html__( '1 week','woolentor' ),
                                    '10'  => esc_html__( '10 days','woolentor' ),
                                    '14'  => esc_html__( '2 weeks','woolentor' ),
                                    '21'  => esc_html__( '3 weeks','woolentor' ),
                                    '28'  => esc_html__( '4 weeks','woolentor' ),
                                    '35'  => esc_html__( '5 weeks','woolentor' ),
                                    '42'  => esc_html__( '6 weeks','woolentor' ),
                                    '49'  => esc_html__( '7 weeks','woolentor' ),
                                    '56'  => esc_html__( '8 weeks','woolentor' ),
                                ),
                                'class'       => 'notification_real woolentor-action-field-left',
                            ),
            
                            array(
                                'name'    => 'notification_animation_area_title',
                                'headding'=> esc_html__( 'Animation', 'woolentor' ),
                                'type'    => 'title',
                                'size'    => 'margin_0 regular',
                                'class'   => 'element_section_title_area',
                            ),
            
                            array(
                                'name'    => 'notification_inanimation',
                                'label'   => esc_html__( 'Animation In', 'woolentor' ),
                                'desc'    => esc_html__( 'Choose entrance animation.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => 'fadeInLeft',
                                'options' => array(
                                    'bounce'            => esc_html__( 'bounce','woolentor' ),
                                    'flash'             => esc_html__( 'flash','woolentor' ),
                                    'pulse'             => esc_html__( 'pulse','woolentor' ),
                                    'rubberBand'        => esc_html__( 'rubberBand','woolentor' ),
                                    'shake'             => esc_html__( 'shake','woolentor' ),
                                    'swing'             => esc_html__( 'swing','woolentor' ),
                                    'tada'              => esc_html__( 'tada','woolentor' ),
                                    'wobble'            => esc_html__( 'wobble','woolentor' ),
                                    'jello'             => esc_html__( 'jello','woolentor' ),
                                    'heartBeat'         => esc_html__( 'heartBeat','woolentor' ),
                                    'bounceIn'          => esc_html__( 'bounceIn','woolentor' ),
                                    'bounceInDown'      => esc_html__( 'bounceInDown','woolentor' ),
                                    'bounceInLeft'      => esc_html__( 'bounceInLeft','woolentor' ),
                                    'bounceInRight'     => esc_html__( 'bounceInRight','woolentor' ),
                                    'bounceInUp'        => esc_html__( 'bounceInUp','woolentor' ),
                                    'fadeIn'            => esc_html__( 'fadeIn','woolentor' ),
                                    'fadeInDown'        => esc_html__( 'fadeInDown','woolentor' ),
                                    'fadeInDownBig'     => esc_html__( 'fadeInDownBig','woolentor' ),
                                    'fadeInLeft'        => esc_html__( 'fadeInLeft','woolentor' ),
                                    'fadeInLeftBig'     => esc_html__( 'fadeInLeftBig','woolentor' ),
                                    'fadeInRight'       => esc_html__( 'fadeInRight','woolentor' ),
                                    'fadeInRightBig'    => esc_html__( 'fadeInRightBig','woolentor' ),
                                    'fadeInUp'          => esc_html__( 'fadeInUp','woolentor' ),
                                    'fadeInUpBig'       => esc_html__( 'fadeInUpBig','woolentor' ),
                                    'flip'              => esc_html__( 'flip','woolentor' ),
                                    'flipInX'           => esc_html__( 'flipInX','woolentor' ),
                                    'flipInY'           => esc_html__( 'flipInY','woolentor' ),
                                    'lightSpeedIn'      => esc_html__( 'lightSpeedIn','woolentor' ),
                                    'rotateIn'          => esc_html__( 'rotateIn','woolentor' ),
                                    'rotateInDownLeft'  => esc_html__( 'rotateInDownLeft','woolentor' ),
                                    'rotateInDownRight' => esc_html__( 'rotateInDownRight','woolentor' ),
                                    'rotateInUpLeft'    => esc_html__( 'rotateInUpLeft','woolentor' ),
                                    'rotateInUpRight'   => esc_html__( 'rotateInUpRight','woolentor' ),
                                    'slideInUp'         => esc_html__( 'slideInUp','woolentor' ),
                                    'slideInDown'       => esc_html__( 'slideInDown','woolentor' ),
                                    'slideInLeft'       => esc_html__( 'slideInLeft','woolentor' ),
                                    'slideInRight'      => esc_html__( 'slideInRight','woolentor' ),
                                    'zoomIn'            => esc_html__( 'zoomIn','woolentor' ),
                                    'zoomInDown'        => esc_html__( 'zoomInDown','woolentor' ),
                                    'zoomInLeft'        => esc_html__( 'zoomInLeft','woolentor' ),
                                    'zoomInRight'       => esc_html__( 'zoomInRight','woolentor' ),
                                    'zoomInUp'          => esc_html__( 'zoomInUp','woolentor' ),
                                    'hinge'             => esc_html__( 'hinge','woolentor' ),
                                    'jackInTheBox'      => esc_html__( 'jackInTheBox','woolentor' ),
                                    'rollIn'            => esc_html__( 'rollIn','woolentor' ),
                                    'rollOut'           => esc_html__( 'rollOut','woolentor' ),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'    => 'notification_outanimation',
                                'label'   => esc_html__( 'Animation Out', 'woolentor' ),
                                'desc'    => esc_html__( 'Choose exit animation.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => 'fadeOutRight',
                                'options' => array(
                                    'bounce'             => esc_html__( 'bounce','woolentor' ),
                                    'flash'              => esc_html__( 'flash','woolentor' ),
                                    'pulse'              => esc_html__( 'pulse','woolentor' ),
                                    'rubberBand'         => esc_html__( 'rubberBand','woolentor' ),
                                    'shake'              => esc_html__( 'shake','woolentor' ),
                                    'swing'              => esc_html__( 'swing','woolentor' ),
                                    'tada'               => esc_html__( 'tada','woolentor' ),
                                    'wobble'             => esc_html__( 'wobble','woolentor' ),
                                    'jello'              => esc_html__( 'jello','woolentor' ),
                                    'heartBeat'          => esc_html__( 'heartBeat','woolentor' ),
                                    'bounceOut'          => esc_html__( 'bounceOut','woolentor' ),
                                    'bounceOutDown'      => esc_html__( 'bounceOutDown','woolentor' ),
                                    'bounceOutLeft'      => esc_html__( 'bounceOutLeft','woolentor' ),
                                    'bounceOutRight'     => esc_html__( 'bounceOutRight','woolentor' ),
                                    'bounceOutUp'        => esc_html__( 'bounceOutUp','woolentor' ),
                                    'fadeOut'            => esc_html__( 'fadeOut','woolentor' ),
                                    'fadeOutDown'        => esc_html__( 'fadeOutDown','woolentor' ),
                                    'fadeOutDownBig'     => esc_html__( 'fadeOutDownBig','woolentor' ),
                                    'fadeOutLeft'        => esc_html__( 'fadeOutLeft','woolentor' ),
                                    'fadeOutLeftBig'     => esc_html__( 'fadeOutLeftBig','woolentor' ),
                                    'fadeOutRight'       => esc_html__( 'fadeOutRight','woolentor' ),
                                    'fadeOutRightBig'    => esc_html__( 'fadeOutRightBig','woolentor' ),
                                    'fadeOutUp'          => esc_html__( 'fadeOutUp','woolentor' ),
                                    'fadeOutUpBig'       => esc_html__( 'fadeOutUpBig','woolentor' ),
                                    'flip'               => esc_html__( 'flip','woolentor' ),
                                    'flipOutX'           => esc_html__( 'flipOutX','woolentor' ),
                                    'flipOutY'           => esc_html__( 'flipOutY','woolentor' ),
                                    'lightSpeedOut'      => esc_html__( 'lightSpeedOut','woolentor' ),
                                    'rotateOut'          => esc_html__( 'rotateOut','woolentor' ),
                                    'rotateOutDownLeft'  => esc_html__( 'rotateOutDownLeft','woolentor' ),
                                    'rotateOutDownRight' => esc_html__( 'rotateOutDownRight','woolentor' ),
                                    'rotateOutUpLeft'    => esc_html__( 'rotateOutUpLeft','woolentor' ),
                                    'rotateOutUpRight'   => esc_html__( 'rotateOutUpRight','woolentor' ),
                                    'slideOutUp'         => esc_html__( 'slideOutUp','woolentor' ),
                                    'slideOutDown'       => esc_html__( 'slideOutDown','woolentor' ),
                                    'slideOutLeft'       => esc_html__( 'slideOutLeft','woolentor' ),
                                    'slideOutRight'      => esc_html__( 'slideOutRight','woolentor' ),
                                    'zoomOut'            => esc_html__( 'zoomOut','woolentor' ),
                                    'zoomOutDown'        => esc_html__( 'zoomOutDown','woolentor' ),
                                    'zoomOutLeft'        => esc_html__( 'zoomOutLeft','woolentor' ),
                                    'zoomOutRight'       => esc_html__( 'zoomOutRight','woolentor' ),
                                    'zoomOutUp'          => esc_html__( 'zoomOutUp','woolentor' ),
                                    'hinge'              => esc_html__( 'hinge','woolentor' ),
                                ),
                                'class' => 'woolentor-action-field-left'
                            ),
                            
                            array(
                                'name'    => 'notification_style_area_title',
                                'headding'=> esc_html__( 'Style', 'woolentor' ),
                                'type'    => 'title',
                                'size'    => 'margin_0 regular',
                                'class' => 'element_section_title_area',
                            ),
            
                            array(
                                'name'        => 'notification_width',
                                'label'       => esc_html__( 'Width', 'woolentor' ),
                                'desc'        => esc_html__( 'You can handle the notificaton width.', 'woolentor' ),
                                'type'        => 'text',
                                'default'     => esc_html__( '550px', 'woolentor' ),
                                'placeholder' => esc_html__( '550px', 'woolentor' ),
                                'class'       => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'        => 'notification_mobile_width',
                                'label'       => esc_html__( 'Width for mobile', 'woolentor' ),
                                'desc'        => esc_html__( 'You can handle the notificaton width.', 'woolentor' ),
                                'type'        => 'text',
                                'default'     => esc_html__( '90%', 'woolentor' ),
                                'placeholder' => esc_html__( '90%', 'woolentor' ),
                                'class'       => 'woolentor-action-field-left'
                            ),
            
                            array(
                                'name'  => 'background_color',
                                'label' => esc_html__( 'Background Color', 'woolentor' ),
                                'desc'  => esc_html__( 'Set the background color of the notification.', 'woolentor' ),
                                'type'  => 'color',
                                'class' => 'notification_real woolentor-action-field-left',
                            ),
            
                            array(
                                'name'  => 'heading_color',
                                'label' => esc_html__( 'Heading Color', 'woolentor' ),
                                'desc'  => esc_html__( 'Set the heading color of the notification.', 'woolentor' ),
                                'type'  => 'color',
                                'class' => 'notification_real woolentor-action-field-left',
                            ),
            
                            array(
                                'name'  => 'content_color',
                                'label' => esc_html__( 'Content Color', 'woolentor' ),
                                'desc'  => esc_html__( 'Set the content color of the notification.', 'woolentor' ),
                                'type'  => 'color',
                                'class' => 'notification_real woolentor-action-field-left',
                            ),
            
                            array(
                                'name'  => 'cross_color',
                                'label' => esc_html__( 'Cross Icon Color', 'woolentor' ),
                                'desc'  => esc_html__( 'Set the cross icon color of the notification.', 'woolentor' ),
                                'type'  => 'color',
                                'class' => 'woolentor-action-field-left'
                            ),

                        )
                    ),

                    array(
                        'name'     => 'shopify_checkout_settings',
                        'label'    => esc_html__( 'Shopify Style Checkout', 'woolentor' ),
                        'type'     => 'module',
                        'default'  => 'off',
                        'section'  => 'woolentor_shopify_checkout_settings',
                        'option_id'=> 'enable',
                        'require_settings'  => true,
                        'setting_fields' => array(

                            array(
                                'name'  => 'enable',
                                'label' => esc_html__( 'Enable / Disable', 'woolentor' ),
                                'desc'  => esc_html__( 'You can enable / disable shopify style checkout page from here.', 'woolentor' ),
                                'type'  => 'checkbox',
                                'default' => 'off',
                                'class' => 'woolentor-action-field-left'
                            ),

                            array(
                                'name'    => 'logo',
                                'label'   => esc_html__( 'Logo', 'woolentor' ),
                                'desc'    => esc_html__( 'You can upload your logo for shopify style checkout page from here.', 'woolentor' ),
                                'type'    => 'image_upload',
                                'options' => [
                                    'button_label'        => esc_html__( 'Upload', 'woolentor' ),   
                                    'button_remove_label' => esc_html__( 'Remove', 'woolentor' ),   
                                ],
                                'class' => 'woolentor-action-field-left'
                            ),

                            array(
                                'name'    => 'custommenu',
                                'label'   => esc_html__( 'Bottom Menu', 'woolentor' ),
                                'desc'    => esc_html__( 'You can choose menu for shopify style checkout page.', 'woolentor' ),
                                'type'    => 'select',
                                'default' => '0',
                                'options' => array( '0'=> esc_html__('Select Menu','woolentor') ) + woolentor_get_all_create_menus(),
                                'class' => 'woolentor-action-field-left'
                            ),
                            
                        )

                    ),

                    array(
                        'name'     => 'woolentor_flash_sale_event_settings',
                        'label'    => esc_html__( 'Flash Sale Countdown', 'woolentor' ),
                        'type'     => 'module',
                        'default'  => 'off',
                        'section'  => 'woolentor_flash_sale_settings',
                        'option_id'=> 'enable',
                        'require_settings'  => true,
                        'setting_fields' => array(
    
                            array(
                                'name'  => 'enable',
                                'label' => esc_html__( 'Enable / Disable', 'woolentor' ),
                                'desc'  => esc_html__( 'You can enable / disable flash sale from here.', 'woolentor' ),
                                'type'  => 'checkbox',
                                'default' => 'off',
                                'class' => 'woolentor-action-field-left'
                            ),
    
                            array(
                                'name'    => 'override_sale_price',
                                'label'   => esc_html__( 'Override Sale Price', 'woolentor' ),
                                'type'    => 'checkbox',
                                'default' => 'off',
                                'class'   => 'woolentor-action-field-left'
                            ),
    
                            array(
                                'name'    => 'enable_countdown_on_product_details_page',
                                'label'   => esc_html__( 'Show Countdown On Product Details Page', 'woolentor' ),
                                'type'    => 'checkbox',
                                'default' => 'on',
                                'class'   => 'woolentor-action-field-left'
                            ),
    
                             array(
                                 'name'        => 'countdown_position',
                                 'label'       => esc_html__( 'Countdown Position', 'woolentor' ),
                                 'type'        => 'select',
                                 'options'     => array(
                                    'woocommerce_before_add_to_cart_form'      => esc_html__('Add to cart - Before', 'woolentor'),
                                    'woocommerce_after_add_to_cart_form'       => esc_html__('Add to cart - After', 'woolentor'),
                                    'woocommerce_product_meta_start'           => esc_html__('Product meta - Before', 'woolentor'),
                                    'woocommerce_product_meta_end'             => esc_html__('Product meta - After', 'woolentor'),
                                    'woocommerce_single_product_summary'       => esc_html__('Product summary - Before', 'woolentor'),
                                    'woocommerce_after_single_product_summary' => esc_html__('Product summary - After', 'woolentor'),
                                 ),
                                 'class'       => 'woolentor-action-field-left'
                             ),
    
                            array(
                                'name'    => 'countdown_timer_title',
                                'label'   => esc_html__( 'Countdown Timer Title', 'woolentor' ),
                                'type'    => 'text',
                                'default' => esc_html__('Hurry Up! Offer ends in', 'woolentor'),
                                'class'   => 'woolentor-action-field-left'
                            ),
    
                            array(
                                'name'        => 'deals',
                                'label'       => esc_html__( 'Sale Events', 'woolentor' ),
                                'desc'        => esc_html__( 'Repeater field description', 'woolentor' ),
                                'type'        => 'repeater',
                                'title_field' => 'title',
                                'fields'  => [
    
                                    array(
                                        'name'        => 'status',
                                        'label'       => esc_html__( 'Enable', 'woolentor' ),
                                        'desc'        => esc_html__( 'Enable / Disable', 'woolentor' ),
                                        'type'        => 'checkbox',
                                        'default'     => 'on',
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'title',
                                        'label'       => esc_html__( 'Event Name', 'woolentor' ),
                                        'type'        => 'text',
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'start_date',
                                        'label'       => esc_html__( 'Valid From', 'woolentor' ),
                                        'desc'        => __( 'The date and time the event should be enabled. Please set time based on your server time settings. Current Server Date / Time: '. current_time('Y M d'), 'woolentor' ),
                                        'type'        => 'date',
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'end_date',
                                        'label'       => esc_html__( 'Valid To', 'woolentor' ),
                                        'desc'        => esc_html__( 'The date and time the event should be disabled.', 'woolentor' ),
                                        'type'        => 'date',
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'apply_on_all_products',
                                        'label'       => esc_html__( 'Apply On All Products', 'woolentor' ),
                                        'type'        => 'checkbox',
                                        'default'     => 'off',
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'categories',
                                        'label'       => esc_html__( 'Select Categories', 'woolentor' ),
                                        'desc'        => esc_html__( 'Select the categories in wich products the discount will be applied.', 'woolentor' ),
                                        'type'        => 'multiselect',
                                        'options'     => woolentor_taxonomy_list('product_cat','term_id'),
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'products',
                                        'label'       => esc_html__( 'Select Products', 'woolentor' ),
                                        'desc'        => esc_html__( 'Select individual products in wich the discount will be applied.', 'woolentor' ),
                                        'type'        => 'multiselect',
                                        'options'     => woolentor_post_name( 'product' ),
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'exclude_products',
                                        'label'       => esc_html__( 'Exclude Products', 'woolentor' ),
                                        'type'        => 'multiselect',
                                        'options'     => woolentor_post_name( 'product' ),
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'        => 'discount_type',
                                        'label'       => esc_html__( 'Discount Type', 'woolentor' ),
                                        'type'        => 'select',
                                        'options'     => array(
                                            'fixed_discount'      => esc_html__( 'Fixed Discount', 'woolentor' ),
                                            'percentage_discount' => esc_html__( 'Percentage Discount', 'woolentor' ),
                                            'fixed_price'         => esc_html__( 'Fixed Price', 'woolentor' ),
                                        ),
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                    array(
                                        'name'  => 'discount_value',
                                        'label' => esc_html__( 'Discount Value', 'woolentor-pro' ),
                                        'min'               => 0.0,
                                        'step'              => 0.01,
                                        'type'              => 'number',
                                        'default'           => '50',
                                        'sanitize_callback' => 'floatval',
                                        'class'             => 'woolentor-action-field-left',
                                    ),
    
                                    array(
                                        'name'        => 'apply_discount_only_for_registered_customers',
                                        'label'       => esc_html__( 'Apply Discount Only For Registered Customers', 'woolentor' ),
                                        'type'        => 'checkbox',
                                        'class'       => 'woolentor-action-field-left'
                                    ),
    
                                ]
                            ),
                            
                        )
    
                    ),
                    
                    array(
                        'name'    => 'ajaxsearch',
                        'label'   => esc_html__( 'Ajax Search Widget', 'woolentor' ),
                        'desc'    => esc_html__( 'AJAX Search Widget', 'woolentor' ),
                        'type'    => 'element',
                        'default' => 'off',
                    ),
    
                    array(
                        'name'     => 'ajaxcart_singleproduct',
                        'label'    => esc_html__( 'Single Product Ajax Add To Cart', 'woolentor' ),
                        'desc'     => esc_html__( 'AJAX Add to Cart on Single Product page', 'woolentor' ),
                        'type'     => 'element',
                        'default'  => 'off',
                    ),

                    array(
                        'name'   => 'partial_paymentp',
                        'label'  => esc_html__( 'Partial Payment', 'woolentor' ),
                        'desc'   => esc_html__( 'Partial Payment Module', 'woolentor' ),
                        'type'   => 'module',
                        'default'=> 'off',
                        'require_settings' => true,
                        'is_pro' => true
                    ),

                    array(
                        'name'   => 'pre_ordersp',
                        'label'  => esc_html__( 'Pre Orders', 'woolentor' ),
                        'desc'   => esc_html__( 'Pre Orders Module', 'woolentor' ),
                        'type'   => 'module',
                        'default'=> 'off',
                        'require_settings' => true,
                        'is_pro' => true
                    ),
                    
                    array(
                        'name'   => 'single_product_sticky_add_to_cartp',
                        'label'  => esc_html__( 'Product sticky Add to cart', 'woolentor' ),
                        'desc'   => esc_html__( 'Sticky Add to Cart on Single Product page', 'woolentor' ),
                        'type'   => 'element',
                        'default'=> 'off',
                        'is_pro' => true
                    ),
    
                    array(
                        'name'   => 'mini_side_cartp',
                        'label'  => esc_html__( 'Side Mini Cart', 'woolentor' ),
                        'type'   => 'element',
                        'default'=> 'off',
                        'is_pro' => true
                    ),

                    array(
                        'name'   => 'redirect_add_to_cartp',
                        'label'  => esc_html__( 'Redirect to Checkout', 'woolentor-pro' ),
                        'type'   => 'element',
                        'default'=> 'off',
                        'is_pro' => true
                    ),
    
                    array(
                        'name'   => 'multi_step_checkoutp',
                        'label'  => esc_html__( 'Multi Step Checkout', 'woolentor' ),
                        'type'   => 'element',
                        'default'=> 'off',
                        'is_pro' => true
                    )

                ),

                'others' => array(

                    array(
                        'name'  => 'loadproductlimit',
                        'label' => esc_html__( 'Load Products in Elementor Addons', 'woolentor' ),
                        'desc'  => esc_html__( 'Set the number of products to load in Elementor Addons', 'woolentor' ),
                        'min'               => 1,
                        'max'               => 100,
                        'step'              => '1',
                        'type'              => 'number',
                        'default'           => '20',
                        'sanitize_callback' => 'floatval'
                    )

                ),

            ),

            'woolentor_style_tabs' => array(

                array(
                    'name'     => 'section_area_title_heading',
                    'type'     => 'title',
                    'headding' => esc_html__( 'Universal layout style options', 'woolentor' ),
                    'size'     => 'woolentor_style_seperator',
                ),

                array(
                    'name'      => 'content_area_bg',
                    'label'     => esc_html__( 'Content area background', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#ffffff',
                ),

                array(
                    'name'      => 'section_title_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Title', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'title_color',
                    'label'     => esc_html__( 'Title color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#444444',
                ),
                array(
                    'name'      => 'title_hover_color',
                    'label'     => esc_html__( 'Title hover color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#dc9a0e',
                ),

                array(
                    'name'      => 'section_price_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Price', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'sale_price_color',
                    'label'     => esc_html__( 'Sale price color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#444444',
                ),
                array(
                    'name'      => 'regular_price_color',
                    'label'     => esc_html__( 'Regular price color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#444444',
                ),

                array(
                    'name'      => 'section_category_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Category', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'category_color',
                    'label'     => esc_html__( 'Category color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#444444',
                ),
                array(
                    'name'      => 'category_hover_color',
                    'label'     => esc_html__( 'Category hover color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#dc9a0e',
                ),

                array(
                    'name'      => 'section_short_description_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Short Description', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'desc_color',
                    'label'     => esc_html__( 'Description color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#444444',
                ),

                array(
                    'name'      => 'section_rating_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Rating', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'empty_rating_color',
                    'label'     => esc_html__( 'Empty rating color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#aaaaaa',
                ),
                array(
                    'name'      => 'rating_color',
                    'label'     => esc_html__( 'Rating color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#dc9a0e',
                ),

                array(
                    'name'      => 'section_badge_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Product Badge', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'badge_color',
                    'label'     => esc_html__( 'Badge color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#444444',
                ),

                array(
                    'name'      => 'section_action_btn_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Quick Action Button', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'tooltip_color',
                    'label'     => esc_html__( 'Tool tip color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#ffffff',
                ),
                array(
                    'name'      => 'btn_color',
                    'label'     => esc_html__( 'Button color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#000000',
                ),
                array(
                    'name'      => 'btn_hover_color',
                    'label'     => esc_html__( 'Button hover color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#dc9a0e',
                ),

                array(
                    'name'      => 'section_action_list_btn_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Archive List View Action Button', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'list_btn_color',
                    'label'     => esc_html__( 'List View Button color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#000000',
                ),
                array(
                    'name'      => 'list_btn_hover_color',
                    'label'     => esc_html__( 'List View Button Hover color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#dc9a0e',
                ),
                array(
                    'name'      => 'list_btn_bg_color',
                    'label'     => esc_html__( 'List View Button background color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#ffffff',
                ),
                array(
                    'name'      => 'list_btn_hover_bg_color',
                    'label'     => esc_html__( 'List View Button hover background color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#ff3535',
                ),

                array(
                    'name'      => 'section_counter_timer_heading',
                    'type'      => 'title',
                    'headding'  => esc_html__( 'Counter Timer', 'woolentor' ),
                    'size'      => 'woolentor_style_seperator',
                ),
                array(
                    'name'      => 'counter_color',
                    'label'     => esc_html__( 'Counter timer color', 'woolentor' ),
                    'desc'      => esc_html__( 'Default Color for universal layout.', 'woolentor' ),
                    'type'      => 'color',
                    'default'   => '#ffffff',
                ),

            ),

        );

        // Post Duplicator Condition
        if( !is_plugin_active('ht-mega-for-elementor/htmega_addons_elementor.php') ){

            $post_types = woolentor_get_post_types( array( 'defaultadd' => 'all' ) );
            if ( did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' ) ) {
                $post_types['elementor_library'] = esc_html__( 'Templates', 'woolentor' );
            }

            $settings_fields['woolentor_others_tabs']['modules'][] = [
                'name'     => 'postduplicator',
                'label'    => esc_html__( 'Post Duplicator', 'woolentor-pro' ),
                'type'     => 'element',
                'default'  => 'off',
                'require_settings'  => true,
                'setting_fields' => array(
                    
                    array(
                        'name'    => 'postduplicate_condition',
                        'label'   => esc_html__( 'Post Duplicator Condition', 'woolentor' ),
                        'desc'    => esc_html__( 'You can enable duplicator for individual post.', 'woolentor' ),
                        'type'    => 'multiselect',
                        'default' => '',
                        'options' => $post_types
                    )

                )
            ];

        }

        // Wishsuite Addons
        if( is_plugin_active('wishsuite/wishsuite.php') ){
            $settings_fields['woolentor_elements_tabs'][] = [
                'name'      => 'wb_wishsuite_table',
                'label'     => esc_html__( 'WishSuite Table', 'woolentor' ),
                'type'      => 'element',
                'default'   => 'on',
            ];
        }

        // Ever Compare Addons
        if( is_plugin_active('ever-compare/ever-compare.php') ){
            $settings_fields['woolentor_elements_tabs'][] = [
                'name'      => 'wb_ever_compare_table',
                'label'     => esc_html__( 'Ever Compare', 'woolentor' ),
                'type'      => 'element',
                'default'   => 'on',
            ];
        }

        // JustTable Addons
        if( is_plugin_active('just-tables/just-tables.php') || is_plugin_active('just-tables-pro/just-tables-pro.php') ){
            $settings_fields['woolentor_elements_tabs'][] = [
                'name'      => 'wb_just_table',
                'label'     => esc_html__( 'JustTable', 'woolentor' ),
                'type'      => 'element',
                'default'   => 'on',
            ];
        }

        // whols Addons
        if( is_plugin_active('whols/whols.php') || is_plugin_active('whols-pro/whols-pro.php') ){
            $settings_fields['woolentor_elements_tabs'][] = [
                'name'    => 'wb_whols',
                'label'   => esc_html__( 'Whols', 'woolentor' ),
                'type'    => 'element',
                'default' => 'on'
            ];
        }

        // Multicurrency Addons
        if( is_plugin_active('wc-multi-currency/wcmilticurrency.php') || is_plugin_active('multicurrencypro/multicurrencypro.php') ){
            $settings_fields['woolentor_elements_tabs'][] = [
                'name'    => 'wb_wc_multicurrency',
                'label'   => esc_html__( 'Multi Currency', 'woolentor' ),
                'type'    => 'element',
                'default' => 'on'
            ];
        }

        return apply_filters( 'woolentor_admin_fields', $settings_fields );

    }



}