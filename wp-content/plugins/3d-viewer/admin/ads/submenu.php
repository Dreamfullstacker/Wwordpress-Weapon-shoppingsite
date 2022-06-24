<?php

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function bp3d_enqueue_custom_admin_style() {
    wp_register_style( 'bp3d_admin_custom_css', plugin_dir_url(__FILE__) . 'style.css', false, '1.0.0' );
    wp_enqueue_style( 'bp3d_admin_custom_css' );
}
add_action( 'admin_enqueue_scripts', 'bp3d_enqueue_custom_admin_style' );


//wp_enqueue_style('bp3d_admin-style', plugin_dir_url(__FILE__) . 'admin/css/style.css');

//-----------------------------------------------
// Helps 
//-----------------------------------------------


add_action('admin_menu', 'bp3d_support_page');

function bp3d_support_page()
{
    add_submenu_page('edit.php?post_type=bp3d-model-viewer', 'Help ', 'Help', 'manage_options', 'bp3d-support', 'bp3d_support_page_callback');
}

function bp3d_support_page_callback()
{
    ?>
    <div class="bplugins-container">
        <div class="row">
            <div class="bplugins-features clearfix">
                <div class="col col-12">
                    <div class="bplugins-feature center">
                        <div style="background:white;overflow:hidden;">
                            <div style="width:128px;heigh:128px;overflow:hidden;float:left;">
                                <img src="https://ps.w.org/3d-viewer/assets/icon-128x128.png?rev=2510528" alt="Logo" width="100" height="100">
                            </div>
                            <div style="float:left; overflow:hidden;text-align:left;">
                                <h1><?php echo esc_html__('Thanks for Installing 3D Viewer Plugin.', 'model-viewer'); ?></h1>
                                <p> <?php echo esc_html__('Please follow the links below to get some helpful resources.', 'model-viewer'); ?></p>
                            </div>
                        </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="bplugins-container">
    <div class="row">
        <div class="bplugins-features">
            <div class="col col-4">
                <div class="bplugins-feature center">
                    <i class="fa fa-life-ring"></i>
                    <h3>Need any Assistance?</h3>
                    <p>Our Expert Support Team is always ready to help you out promptly.</p>
                    <a href="https://bplugins.com/support/" target="_blank" class="button
                    button-primary">Contact Support</a>
                </div>
            </div>
            <div class="col col-4">
                <div class="bplugins-feature center">
                    <i class="fa fa-file-text"></i>
                    <h3>Looking for Documentation?</h3>
                    <p>We have detailed documentation on every aspects of the plugin.</p>
                    <a href="https://3d-viewer.bplugins.com/" target="_blank" class="button button-primary">Documentation</a>
                </div>
            </div>

            <div class="col col-4">
                <div class="bplugins-feature center">
                    <i class="fa fa-thumbs-up"></i>
                    <h3>Liked This Plugin?</h3>
                    <p>Glad to know that, you can support us by leaving a 5 &#11088; rating.</p>
                    <a href="https://wordpress.org/support/plugin/3d-viewer/reviews/#new-post" target="_blank" class="button
                    button-primary">Rate the Plugin</a>
                </div>
            </div>            
        </div>
    </div>
</div>

<div class="bplugins-container">
    <div class="row">
        <div class="bplugins-features">
            <div class="col col-12">
                <div class="bplugins-feature center">
                    <h1>Video Tutorials</h1><br/>
                    <div class="embed-container"><iframe width="100%" height="700px" src="https://www.youtube.com/embed/PSXtvy1rF88" frameborder="0"
                    allowfullscreen></iframe></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}







