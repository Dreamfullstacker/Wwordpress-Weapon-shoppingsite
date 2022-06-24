<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WL_Product_Description_Element extends Widget_Base {

    public function get_name() {
        return 'wl-single-product-description';
    }

    public function get_title() {
        return __( 'WL: Product Description', 'woolentor' );
    }

    public function get_icon() {
        return 'eicon-product-description';
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
        return ['description','product description','product content'];
    }

    protected function register_controls() {

        // Product Style
        $this->start_controls_section(
            'product_style_section',
            array(
                'label' => __( 'Style', 'woolentor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
            $this->add_responsive_control(
                'text_align',
                [
                    'label' => __( 'Alignment', 'woolentor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __( 'Left', 'woolentor' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'woolentor' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => __( 'Right', 'woolentor' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                        'justify' => [
                            'title' => __( 'Justified', 'woolentor' ),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'text-align: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'text_color',
                [
                    'label' => __( 'Text Color', 'woolentor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '.woocommerce {{WRAPPER}} .woocommerce_product_description' => 'color: {{VALUE}} !important',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'text_typography',
                    'label' => __( 'Typography', 'woolentor' ),
                    'selector' => '.woocommerce {{WRAPPER}} .woocommerce_product_description',
                ]
            );

        $this->end_controls_section();

    }


    protected function render( $instance = [] ) {
       global $product, $post;
        $product = wc_get_product();
        if ( Plugin::instance()->editor->is_edit_mode() ) {
            echo '<div class="woocommerce_product_description">'.\WooLentor_Default_Data::instance()->default( $this->get_name() ).'</div>';
        }else{
            if ( empty( $product ) ) { return; }
            echo '<div class="woocommerce_product_description">';
                the_content();
            echo '</div>';
            //echo '<div class="woocommerce_product_description">'.$post->post_content.'</div>';
            //echo '<div class="woocommerce_product_description">'.$product->get_description().'</div>';

        }
    }

}
Plugin::instance()->widgets_manager->register_widget_type( new WL_Product_Description_Element() );
