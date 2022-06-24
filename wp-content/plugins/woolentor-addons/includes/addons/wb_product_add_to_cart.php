<?php
namespace Elementor;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WL_Product_Add_To_Cart_Element extends Widget_Base {

    public function get_name() {
        return 'wl-product-add-to-cart';
    }
    
    public function get_title() {
        return __( 'WL: Add To cart', 'woolentor' );
    }

    public function get_icon() {
        return 'eicon-product-add-to-cart';
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
        return ['add to cart','cart','button','buy now'];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'add_to_cart_button_style',
            [
                'label' => __( 'Button', 'woolentor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->start_controls_tabs('button_normal_style_tabs');
                
                // Button Normal tab
                $this->start_controls_tab(
                    'button_normal_style_tab',
                    [
                        'label' => __( 'Normal', 'woolentor' ),
                    ]
                );
                    
                    $this->add_control(
                        'button_color',
                        [
                            'label'     => __( 'Text Color', 'woolentor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .cart button' => 'color: {{VALUE}} !important;',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Typography::get_type(),
                        array(
                            'name'      => 'button_typography',
                            'label'     => __( 'Typography', 'woolentor' ),
                            'selector'  => '{{WRAPPER}} .cart button',
                        )
                    );

                    $this->add_control(
                        'button_padding',
                        [
                            'label' => __( 'Padding', 'woolentor' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .cart button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                            ],
                        ]
                    );

                    $this->add_control(
                        'button_margin',
                        [
                            'label' => __( 'Margin', 'woolentor' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', 'em' ],
                            'selectors' => [
                                '.woocommerce {{WRAPPER}} form.cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'button_border',
                            'label' => __( 'Border', 'woolentor' ),
                            'selector' => '{{WRAPPER}} .cart button',
                        ]
                    );

                    $this->add_control(
                        'button_border_radius',
                        [
                            'label' => __( 'Border Radius', 'woolentor' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .cart button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                            ],
                        ]
                    );

                    $this->add_control(
                        'button_background_color',
                        [
                            'label' => __( 'Background Color', 'woolentor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .cart button' => 'background-color: {{VALUE}} !important',
                            ],
                        ]
                    );

                $this->end_controls_tab();

                // Button Hover tab
                $this->start_controls_tab(
                    'button_hover_style_tab',
                    [
                        'label' => __( 'Hover', 'woolentor' ),
                    ]
                ); 
                    
                    $this->add_control(
                        'button_hover_color',
                        [
                            'label'     => __( 'Text Color', 'woolentor' ),
                            'type'      => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .cart button:hover' => 'color: {{VALUE}} !important;',
                            ],
                        ]
                    );

                    $this->add_control(
                        'button_hover_background_color',
                        [
                            'label' => __( 'Background Color', 'woolentor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .cart button:hover' => 'background-color: {{VALUE}} !important',
                            ],
                        ]
                    );

                    $this->add_control(
                        'button_hover_border_color',
                        [
                            'label' => __( 'Border Color', 'woolentor' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .cart button:hover' => 'border-color: {{VALUE}} !important',
                            ],
                        ]
                    );

                $this->end_controls_tab();

            $this->end_controls_tabs();

            
        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings();
        global $product;
        $product = wc_get_product();
        if ( Plugin::instance()->editor->is_edit_mode() ) {
            echo \WooLentor_Default_Data::instance()->default( $this->get_name() );
            // echo '<div class="add-to-cart-button">'.$cart_btn.'</div>';
        }else{
            if ( empty( $product ) ) { return; }
            ?>
                <div class="<?php echo esc_attr( wc_get_product()->get_type() ); ?>">
                    <?php woocommerce_template_single_add_to_cart(); ?>
                </div>
            <?php
        }

    }

}

Plugin::instance()->widgets_manager->register_widget_type( new WL_Product_Add_To_Cart_Element() );