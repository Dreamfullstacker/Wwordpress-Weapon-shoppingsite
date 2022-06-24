<?php

/*
 * Plugin Name: 3D Viewer
 * Plugin URI:  https://bplugins.com/
 * Description: Easily display interactive 3D models on the web. Supported File type .glb, .gltf
 * Version: 1.2.9
 * Author: bPlugins LLC
 * Author URI: http://bplugins.com
 * License: GPLv3
 * Text Domain:  model-viewer
 * Domain Path:  /languages
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'bp3dv_fs' ) ) {
    bp3dv_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'bp3dv_fs' ) ) {
        // Create a helper function for easy SDK access.
        function bp3dv_fs()
        {
            global  $bp3dv_fs ;
            
            if ( !isset( $bp3dv_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $bp3dv_fs = fs_dynamic_init( array(
                    'id'             => '8795',
                    'slug'           => '3d-viewer',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_5e6ce3f226c86e3b975b59ed84d6a',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => false,
                ),
                    'menu'           => array(
                    'slug'       => 'edit.php?post_type=bp3d-model-viewer',
                    'first-path' => 'edit.php?post_type=bp3d-model-viewer&page=bp3d-support',
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $bp3dv_fs;
        }
        
        // Init Freemius.
        bp3dv_fs();
        // Signal that SDK was initiated.
        do_action( 'bp3dv_fs_loaded' );
    }
    
    /*Some Set-up*/
    define( 'BP3D_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
    define( 'BP3D_VERSION', '1.2.9' );
    define( 'BP3D_IMPORT_VER', '1.0.0' );
    // load text domain
    function bp3dviewer_load_textdomain()
    {
        load_plugin_textdomain( 'model-viewer', false, dirname( __FILE__ ) . "/languages" );
    }
    
    function bp3d_isset( $array, $key, $default = false )
    {
        if ( isset( $array[$key] ) ) {
            return $array[$key];
        }
        return $default;
    }
    
    add_action( "plugins_loaded", 'bp3dviewer_load_textdomain' );
    //  3d Viewer Assets
    function bp3d_3dviewer_assets()
    {
        wp_enqueue_style( 'bp3d-slick-theme', plugin_dir_url( __FILE__ ) . 'public/css/slick-theme.css' );
        wp_enqueue_style( 'bp3d-slick-css', plugin_dir_url( __FILE__ ) . 'public/css/slick.css' );
        wp_register_style( 'bp3d-custom-style', plugin_dir_url( __FILE__ ) . 'public/css/custom-style.css' );
        wp_enqueue_style( 'bp3d-custom-style' );
        // Scripts
        echo  wp_get_script_tag( array(
            'src'  => BP3D_PLUGIN_DIR . 'public/js/model-viewer.min.js',
            'type' => 'module',
        ) ) ;
        wp_register_script(
            'bp3d-slick',
            plugin_dir_url( __FILE__ ) . 'public/js/slick.min.js',
            [ 'jquery' ],
            BP3D_VERSION,
            true
        );
        wp_register_script(
            'bp3d-script',
            plugin_dir_url( __FILE__ ) . 'dist/public.js',
            [ 'jquery', 'bp3d-slick' ],
            BP3D_VERSION,
            true
        );
        wp_enqueue_script( 'bp3d-script' );
        wp_enqueue_script( 'bp3d-slick' );
        wp_localize_script( 'bp3d-script', 'assetsUrl', [
            'siteUrl'   => site_url(),
            'assetsUrl' => plugin_dir_url( __FILE__ ) . '/public',
        ] );
    }
    
    add_action( 'wp_enqueue_scripts', 'bp3d_3dviewer_assets' );
    // 3d Viewer admin style
    function bp3d_admin_style()
    {
        //script
        wp_enqueue_script(
            'bp3d-admin-script',
            plugin_dir_url( __FILE__ ) . 'public/js/admin-script.js',
            [ 'jquery' ],
            BP3D_VERSION,
            true
        );
        // style
        wp_register_style(
            'bp3d-admin-style',
            plugin_dir_url( __FILE__ ) . 'public/css/admin-style.css',
            '',
            BP3D_VERSION
        );
        wp_register_style(
            'bp3d-readonly-style',
            plugin_dir_url( __FILE__ ) . 'public/css/readonly.css',
            '',
            BP3D_VERSION
        );
        wp_enqueue_style( 'bp3d-admin-style' );
        wp_enqueue_style( 'bp3d-readonly-style' );
    }
    
    add_action( 'admin_enqueue_scripts', 'bp3d_admin_style' );
    // External files Inclusion
    require_once 'inc/mimes/enable-mime-type.php';
    require_once 'inc/csf/csf-config.php';
    require_once 'admin/ads/submenu.php';
    require_once 'inc/viewer-data-importer.php';
    // Shortcode and Freemius Conditional Files
    // free version code
    
    if ( bp3dv_fs()->is_free_plan() ) {
        include "public/shortcode/shortcode.php";
        require_once 'inc/metabox-options-free.php';
        require_once 'inc/bp3d-product-settings.php';
        // Get Option.
        $settings = get_option( '_bp3d_settings_' );
        
        if ( $settings['3d_woo_switcher'] !== "0" ) {
            require_once 'inc/bp3d-product-metabox.php';
            require_once 'inc/bp3d-product-viewer.php';
        }
    
    }
    
    // Custom post-type
    function bp_3d_viewer()
    {
        $labels = array(
            'name'           => __( '3D Viewer', 'model-viewer' ),
            'menu_name'      => __( '3D Viewer', 'model-viewer' ),
            'name_admin_bar' => __( '3D Viewer', 'model-viewer' ),
            'add_new'        => __( 'Add New', 'model-viewer' ),
            'add_new_item'   => __( 'Add New ', 'model-viewer' ),
            'new_item'       => __( 'New 3D Viewer ', 'model-viewer' ),
            'edit_item'      => __( 'Edit 3D Viewer ', 'model-viewer' ),
            'view_item'      => __( 'View 3D Viewer ', 'model-viewer' ),
            'all_items'      => __( 'All 3D Viewers', 'model-viewer' ),
            'not_found'      => __( 'Sorry, we couldn\'t find the Feed you are looking for.' ),
        );
        $args = array(
            'labels'          => $labels,
            'description'     => __( '3D Viewer Options.', 'model-viewer' ),
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'menu_icon'       => 'dashicons-format-image',
            'query_var'       => true,
            'rewrite'         => array(
            'slug' => 'model-viewer',
        ),
            'capability_type' => 'post',
            'has_archive'     => false,
            'hierarchical'    => false,
            'menu_position'   => 20,
            'supports'        => array( 'title' ),
        );
        register_post_type( 'bp3d-model-viewer', $args );
    }
    
    add_action( 'init', 'bp_3d_viewer' );
    //
    /*-------------------------------------------------------------------------------*/
    /*   Additional Features
         /*-------------------------------------------------------------------------------*/
    // Hide & Disabled View, Quick Edit and Preview Button
    function bp3d_remove_row_actions( $idtions )
    {
        global  $post ;
        
        if ( $post->post_type == 'bp3d-model-viewer' ) {
            unset( $idtions['view'] );
            unset( $idtions['inline hide-if-no-js'] );
        }
        
        return $idtions;
    }
    
    if ( is_admin() ) {
        add_filter(
            'post_row_actions',
            'bp3d_remove_row_actions',
            10,
            2
        );
    }
    // HIDE everything in PUBLISH metabox except Move to Trash & PUBLISH button
    function bp3d_hide_publishing_actions()
    {
        global  $post ;
        if ( $post->post_type == 'bp3d-model-viewer' ) {
            echo  '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ' ;
        }
    }
    
    add_action( 'admin_head-post.php', 'bp3d_hide_publishing_actions' );
    add_action( 'admin_head-post-new.php', 'bp3d_hide_publishing_actions' );
    /*-------------------------------------------------------------------------------*/
    // Remove post update massage and link
    /*-------------------------------------------------------------------------------*/
    function bp3d_updated_messages( $messages )
    {
        $messages['bp3d-model-viewer'][1] = __( 'Shortcode updated ', 'model-viewer' );
        return $messages;
    }
    
    add_filter( 'post_updated_messages', 'bp3d_updated_messages' );
    /*-------------------------------------------------------------------------------*/
    /* Change publish button to save.
       /*-------------------------------------------------------------------------------*/
    add_filter(
        'gettext',
        'bp3d_change_publish_button',
        10,
        2
    );
    function bp3d_change_publish_button( $translation, $text )
    {
        if ( 'bp3d-model-viewer' == get_post_type() ) {
            if ( $text == 'Publish' ) {
                return 'Save';
            }
        }
        return $translation;
    }
    
    /*-------------------------------------------------------------------------------*/
    /* Footer Review Request .
       /*-------------------------------------------------------------------------------*/
    add_filter( 'admin_footer_text', 'bp3d_admin_footer' );
    function bp3d_admin_footer( $text )
    {
        
        if ( 'bp3d-model-viewer' == get_post_type() ) {
            $url = 'https://wordpress.org/plugins/3d-viewer/reviews/?filter=5#new-post';
            $text = sprintf( __( 'If you like <strong> 3D Viewer </strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'model-viewer' ), $url );
        }
        
        return $text;
    }
    
    /*-------------------------------------------------------------------------------*/
    /* Shortcode Generator area  .
       /*-------------------------------------------------------------------------------*/
    add_action( 'edit_form_after_title', 'bp3d_shortcode_area' );
    function bp3d_shortcode_area()
    {
        global  $post ;
        
        if ( $post->post_type == 'bp3d-model-viewer' ) {
            ?>	
        <div class="bp3d_shortcode">
            <div class="shortcode-heading">
                <div class="icon"><span class="dashicons dashicons-shortcode"></span> <?php 
            _e( "SHORTCODE", "model-viewer" );
            ?></div>
                <div class="text"> <a href="https://bplugins.com/support/" target="_blank"><?php 
            _e( "Supports", "model-viewer" );
            ?></a></div>
            </div>
            <div class="shortcode-left">
                <h3><?php 
            _e( "Shortcode", "model-viewer" );
            ?></h3>
                <p><?php 
            _e( "Copy and paste this shortcode into your posts, pages and widget:", "model-viewer" );
            ?></p>
                <div class="shortcode" selectable>[3d_viewer id="<?php 
            echo  esc_attr( $post->ID ) ;
            ?>"]</div>
            </div>
            <div class="shortcode-right">
                <h3><?php 
            _e( "Template Include", "model-viewer" );
            ?></h3>
                <p><?php 
            _e( "Copy and paste the PHP code into your template file:", "model-viewer" );
            ?></p>
                <div class="shortcode">&lt;?php echo do_shortcode('[3d_viewer id="<?php 
            echo  esc_html( $post->ID ) ;
            ?>"]');
                ?&gt;</div>
            </div>
        </div>

    <?php 
        }
    
    }
    
    // CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
    add_filter( 'manage_bp3d-model-viewer_posts_columns', 'bp3d_columns_head_only', 10 );
    add_action(
        'manage_bp3d-model-viewer_posts_custom_column',
        'bp3d_columns_content_only',
        10,
        2
    );
    // CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
    function bp3d_columns_head_only( $defaults )
    {
        unset( $defaults['date'] );
        $defaults['directors_name'] = 'ShortCode';
        $defaults['date'] = 'Date';
        return $defaults;
    }
    
    function bp3d_columns_content_only( $column_name, $post_ID )
    {
        if ( $column_name == 'directors_name' ) {
            echo  '<div class="bpbc_front_shortcode"><input onfocus="this.select();" style="text-align: center; border: none; outline: none; background-color: #1e8cbe; color: #fff; padding: 4px 10px; border-radius: 3px;" value="[3d_viewer  id=' . "'" . esc_attr( $post_ID ) . "'" . ']" ></div>' ;
        }
    }
    
    // After activation redirect
    register_activation_hook( __FILE__, 'bp3d_plugin_activate' );
    add_action( 'admin_init', 'bp3d_plugin_redirect' );
    function bp3d_plugin_activate()
    {
        add_option( 'bp3d_plugin_do_activation_redirect', true );
    }
    
    function bp3d_plugin_redirect()
    {
        
        if ( get_option( 'bp3d_plugin_do_activation_redirect', false ) ) {
            delete_option( 'bp3d_plugin_do_activation_redirect' );
            //wp_redirect('edit.php?post_type=bp3d-model-viewer&page=bp3d-support');
        }
    
    }
    
    // Re-ordering 3D Order menu
    function order3dviewerSubMenu( $menu_ord )
    {
        global  $submenu ;
        $arr = array();
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][5] ) ) {
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][5];
        }
        // All 3D Viewers
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][10] ) ) {
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][10];
        }
        // Add New
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][12] ) ) {
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][12];
        }
        // 3D Viewer Settings
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][11] ) ) {
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][11];
        }
        // Help
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][13] ) ) {
            // Account
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][13];
        }
        //
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][14] ) ) {
            // Contact Us
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][14];
        }
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][15] ) ) {
            // Support Forum
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][15];
        }
        if ( isset( $submenu['edit.php?post_type=bp3d-model-viewer'][16] ) ) {
            // Upgrade
            $arr[] = $submenu['edit.php?post_type=bp3d-model-viewer'][16];
        }
        $submenu['edit.php?post_type=bp3d-model-viewer'] = $arr;
        return $menu_ord;
    }
    
    add_filter( 'custom_menu_order', 'order3dviewerSubMenu' );
}
