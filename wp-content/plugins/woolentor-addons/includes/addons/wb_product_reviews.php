<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WL_Product_Reviews_Element extends Widget_Base {

    public function get_name() {
        return 'wl-single-product-reviews';
    }

    public function get_title() {
        return __( 'WL: Product Reviews', 'woolentor' );
    }

    public function get_icon() {
        return 'eicon-product-rating';
    }

    public function get_categories() {
        return array( 'woolentor-addons' );
    }

    public function get_style_depends(){
        return [
            'woolentor-widgets',
        ];
    }

    public function get_keywords(){
        return ['reviews','product review','review form','form'];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Product Reviews', 'woolentor' ),
            )
        );

            $this->add_control(
                'html_notice',
                array(
                    'label' => __( 'Element Information', 'woolentor' ),
                    'show_label' => false,
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => __( 'Products reviews', 'woolentor' ),
                )
            );

        $this->end_controls_section();

    }


    protected function render( $instance = [] ) {

        $settings   = $this->get_settings_for_display();
        global $product;
        $product = wc_get_product();

        if( Plugin::instance()->editor->is_edit_mode() ){
            echo \WooLentor_Default_Data::instance()->default( $this->get_name() );
        } else{
            if ( empty( $product ) ) { return; }
            add_filter( 'comments_template', array( 'WC_Template_Loader', 'comments_template_loader' ) );
            echo '<div class="woocommerce-tabs-list">';
                comments_template();
            echo '</div>';
        }

    }

}
Plugin::instance()->widgets_manager->register_widget_type( new WL_Product_Reviews_Element() );
