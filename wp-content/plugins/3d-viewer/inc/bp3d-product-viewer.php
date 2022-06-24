<?php 
// Woocommerce for 3D Viewer
function bp3d_product_model_viewer() {

    // Options Data
    $id = get_the_ID();
    $modeview_3d = false;
    $settings_opt = false;
    if($id){
        $modeview_3d  = get_post_meta( $id, '_bp3d_product_', true);
        $settings_opt = get_option('_bp3d_settings_');
    }else {
        $id = uniqid();
    }

    // $src = '';

    // if( $modeview_3d && is_array($modeview_3d)) :
    //     if( !isset( $modeview_3d['bp_3d_src']) || empty($modeview_3d['bp_3d_src'] )){
    //         $src  = 'i-do-not-exist.glb';
    //     }else {
    //         $src =  $modeview_3d['bp_3d_src'];
    //     }
    // endif;

    // Model Source
    $models = $modeview_3d['bp3d_models'] ? $modeview_3d['bp3d_models'] : [];

    $alt            = get_the_title();

    $camera_controls = $settings_opt['bp_camera_control'] !== '0' ? 'camera-controls' : '';

    $auto_rotate    = $settings_opt['bp_3d_rotate'] !== '0' ? 'auto-rotate' : '';
    $zooming_3d     = $settings_opt['bp_3d_zooming'] !== '0' ? '' : 'disable-zoom';

    // Preload
    $loading   = isset($settings_opt['bp_3d_loading']) ? $settings_opt['bpp_3d_loading'] : '';
    // AutoPlay and Shadow Intensity
    $model_autoplay = isset($settings_opt['bp_3d_autoplay']) && $settings_opt['bp_3d_autoplay'] !== '0'? 'autoplay': '';

?>

<!-- 3D Model html -->
<?php if( count($models) > 1): ?>

    <div class="bp3dmodel-carousel" data-fullscreen='<?php echo esc_attr($settings_opt['bp_3d_fullscreen']); ?>'>
        <?php foreach( $models as $carousel_model ): ?>
        <div class="bp3dmodel-item">
        <div class="bp_model_gallery">
            <model-viewer class="model" id="bp_model_id_<?php echo esc_attr($id); ?>" <?php echo esc_attr($model_autoplay); ?> ar src="<?php echo esc_url($carousel_model['model_src']); ?>" alt="<?php echo esc_attr($alt); ?>" <?php echo esc_attr($camera_controls); ?> <?php echo esc_attr($zooming_3d); ?> loading="<?php  echo esc_attr($loading); ?>" >
            <?php
                if($settings_opt['bp_3d_progressbar'] !== '1') { ?>
                    <style>
                        model-viewer<?php echo '#bp_model_id_'.esc_attr($id); ?>::part(default-progress-bar) {
                            display:none;
                        }
                    </style>
                <?php 
                } else {

                }
            ?>
            </model-viewer>

        </div>
        </div>
        <?php endforeach; ?>
    </div> <!-- End Of Carousel -->

<?php else: ?>

<div class="bp_grand">   
<div class="bp_model_parent">
<?php 


foreach( $models as $model ): ?>
<model-viewer class="model" id="bp_model_id_<?php echo esc_attr($id); ?>" <?php echo esc_attr($model_autoplay); ?> ar src="<?php echo esc_url($model['model_src']); ?>" alt="<?php echo esc_attr($alt); ?>" <?php echo esc_attr($camera_controls); ?> <?php echo esc_attr($zooming_3d); ?> loading="<?php  echo esc_attr($loading); ?>" >
<?php
    if($settings_opt['bp_3d_progressbar'] !== '1') { ?>
        <style>
             model-viewer<?php echo '#bp_model_id_'.esc_attr($id); ?>::part(default-progress-bar) {
                display:none;
            }
        </style>
    <?php 
    } else { 

    }
?>
</model-viewer>
<?php endforeach; ?>
    <?php if( $settings_opt['bp_3d_fullscreen'] == 1): ?>
    <!-- Button -->
    <svg id="openBtn" width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#f2f2f2" class="bi bi-arrows-fullscreen">
        <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707zm0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707zm-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707z"/>
    </svg>

    <svg id="closeBtn" width="34px" height="34px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path fill="none" stroke="#f2f2f2" stroke-width="2" d="M7,7 L17,17 M7,17 L17,7"/>
    </svg>
    <!-- ./Button -->
    <?php endif; ?>
</div>
</div>  <!-- End of Simple Model -->
<?php endif; ?> 
    <!-- Model Viewer Style -->
    <style>
    <?php echo '#bp_model_id_'.esc_attr($id); ?> {
        width: 100%;
        min-height: 340px;
        background-color: <?php echo esc_attr($modeview_3d['bp_model_bg']); ?>;
    }
    .fullscreen <?php echo "#bp_model_id_".esc_attr($id); ?>{
      height: 100%;
    }
    model-viewer.model {
        --poster-color: transparent;
    }
    </style>
    <?php  
//    echo ob_get_clean();
}
add_action('bp3d_product_model_before', 'bp3d_product_model_viewer');
add_action('bp3d_product_model_after', 'bp3d_product_model_viewer');

// hook run
add_action('woocommerce_loaded', function(){
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    //remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);
    add_action('woocommerce_before_single_product_summary','bp3d_product_models', 20);
    //add_action('woocommerce_product_thumbnails','bp3d_product_thumbnail', 20);
});

function bp3d_product_models(){
    require_once __DIR__.'/bp3d-product-models.php';
}













