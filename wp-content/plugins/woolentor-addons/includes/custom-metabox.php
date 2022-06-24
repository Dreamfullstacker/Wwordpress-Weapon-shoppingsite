<?php

    // add extra metabox tab to woocommerce
    if( !function_exists('woolentor_add_wc_extra_metabox_tab')){
        function woolentor_add_wc_extra_metabox_tab($tabs){
            $woolentor_tab = array(
                'label'    => __( 'Product Badge', 'woolentor' ),
                'target'   => 'woolentor_product_data',
                'class'    => '',
                'priority' => 80,
            );
            $tabs['woolentor_product_badge'] = $woolentor_tab;
            return $tabs;
        }
        add_filter( 'woocommerce_product_data_tabs', 'woolentor_add_wc_extra_metabox_tab' );
    }

    // add metabox to general tab
    if( !function_exists('woolentor_add_metabox_to_general_tab')){
        function woolentor_add_metabox_to_general_tab(){
            echo '<div id="woolentor_product_data" class="panel woocommerce_options_panel hidden">';
                woocommerce_wp_text_input( array(
                    'id'          => '_saleflash_text',
                    'label'       => __( 'Custom Product Badge Text', 'woolentor' ),
                    'placeholder' => __( 'New', 'woolentor' ),
                    'description' => __( 'Enter your preferred Sale badge text. Ex: New / Free etc (Only for Universal layout addon)', 'woolentor' ),
                    'desc_tip' => true
                ) );
            echo '</div>';
        }
        add_action( 'woocommerce_product_data_panels', 'woolentor_add_metabox_to_general_tab' );
    }
    // Update data
    if( !function_exists('woolentor_save_metabox_of_general_tab') ){
        function woolentor_save_metabox_of_general_tab( $post_id ){
            $saleflash_text = wp_kses_post( stripslashes( $_POST['_saleflash_text'] ) );
            update_post_meta( $post_id, '_saleflash_text', $saleflash_text);
        }
        add_action( 'woocommerce_process_product_meta', 'woolentor_save_metabox_of_general_tab');
    }

?>