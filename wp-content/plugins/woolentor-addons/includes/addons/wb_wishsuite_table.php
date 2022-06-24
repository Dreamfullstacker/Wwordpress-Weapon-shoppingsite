<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WL_Wishsuite_Table_Element extends Widget_Base {

    public function get_name() {
        return 'wb-wishsuite-table';
    }

    public function get_title() {
        return __( 'WL: WishSuite Table', 'woolentor' );
    }

    public function get_icon() {
        return 'eicon-table';
    }

    public function get_categories() {
        return array( 'woolentor-addons' );
    }

    public function get_style_depends(){
        return [
            'wishsuite-frontend',
            'woolentor-widgets',
        ];
    }

    public function get_script_depends(){
        return ['wishsuite-frontend'];
    }

    public function get_keywords(){
        return ['wishlist','product wishlist','wishsuite'];
    }

    protected function register_controls() {

        // Content
        $this->start_controls_section(
            'wishsuite_content',
            [
                'label' => __( 'WishSuite', 'woolentor' ),
            ]
        );

            $this->add_control(
                'empty_table_text',
                [
                    'label' => __( 'Empty table text', 'woolentor' ),
                    'type' => Controls_Manager::TEXT,
                    'label_block'=>true,
                ]
            );

        $this->end_controls_section();

        // Table Heading Style
        $this->start_controls_section(
            'table_heading_style_section',
            [
                'label' => __( 'Table Heading', 'woolentor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_control(
                'heading_color',
                [
                    'label' => __( 'Heading Color', 'woolentor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wishsuite-table-content table thead > tr th' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'heading_background',
                    'label' => __( 'Heading Background', 'woolentor' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .wishsuite-table-content table thead > tr th',
                    'exclude' =>['image'],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'heading_border',
                    'label' => __( 'Border', 'woolentor' ),
                    'selector' => '{{WRAPPER}} .wishsuite-table-content table thead > tr',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'heading_typography',
                    'label' => __( 'Typography', 'woolentor' ),
                    'selector' => '{{WRAPPER}} .wishsuite-table-content table thead > tr th',
                ]
            );
            
        $this->end_controls_section();

        // Table Content Style
        $this->start_controls_section(
            'table_content_style_section',
            [
                'label' => __( 'Table Body', 'woolentor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'table_body_border',
                    'label' => __( 'Border', 'woolentor' ),
                    'selector' => '{{WRAPPER}} .wishsuite-table-content table,.wishsuite-table-content table tbody > tr',
                ]
            );

        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {
        $settings   = $this->get_settings_for_display();

        $short_code_attributes = [
            'empty_text' => $settings['empty_table_text'],
        ];
        echo woolentor_do_shortcode( 'wishsuite_table', $short_code_attributes );

    }

}
Plugin::instance()->widgets_manager->register_widget_type( new WL_Wishsuite_Table_Element() );
