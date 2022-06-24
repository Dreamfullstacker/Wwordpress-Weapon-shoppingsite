<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WL_Product_Rating_Element extends Widget_Base {

    public function get_name() {
        return 'wl-single-product-rating';
    }

    public function get_title() {
        return __( 'WL: Product Rating', 'woolentor' );
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
        return ['product rating','rating'];
    }

    protected function register_controls() {

        // Product Rating Style
        $this->start_controls_section(
            'product_rating_style_section',
            array(
                'label' => __( 'Style', 'woolentor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
            $this->add_control(
                'product_rating_color',
                [
                    'label'     => __( 'Star Color', 'woolentor' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .star-rating' => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} .star-rating span:before' => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} .woocommerce-product-rating' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_control(
                'product_rating_text_color',
                [
                    'label'     => __( 'Link Color', 'woolentor' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a.woocommerce-review-link' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                array(
                    'name'      => 'product_rating_link_typography',
                    'label'     => __( 'Link Typography', 'woolentor' ),
                    'selector'  => '{{WRAPPER}} a.woocommerce-review-link',
                )
            );

            $this->add_control(
                'rating_margin',
                [
                    'label' => __( 'Margin', 'woolentor' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em' ],
                    'selectors' => [
                        '.woocommerce {{WRAPPER}} .woocommerce-product-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    ],
                ]
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
            woocommerce_template_single_rating();
        }

    }

}
Plugin::instance()->widgets_manager->register_widget_type( new WL_Product_Rating_Element() );