<?php

//Lets register our shortcode
function bp3dviewer_cpt_content_func( $atts ){
	extract( shortcode_atts( array(
		'id' => '',
        'src' => '',
		'alt' => '',
		'width' => '100%',
		'height' => '%',
		'auto_rotate' => 'auto-rotate',
		'camera_controls' =>'camera-controls',
        'zooming_3d' => '',
        'loading' => '',
	), $atts ) ); ob_start(); ?>	
 
<?php 


// Options Data
$modeview_3d = false;
if($id){
    $modeview_3d = get_post_meta( $id, '_bp3dimages_', true );
}else {
    $id = uniqid();
}

if( $modeview_3d && is_array($modeview_3d)) :
    if( is_array($modeview_3d['bp_3d_src']) && !empty($modeview_3d['bp_3d_src']['url'] )){
        $src  = $modeview_3d['bp_3d_src']['url'] ?? 'i-do-not-exist.glb';
    }
    if( is_array($modeview_3d['bp_3d_width']) && !empty($modeview_3d['bp_3d_width']['width'] )){
        $width = $modeview_3d['bp_3d_width']['width'].$modeview_3d['bp_3d_width']['unit'];
    }
    if( is_array($modeview_3d['bp_3d_height']) && !empty($modeview_3d['bp_3d_height']['height'] )){
        $height = $modeview_3d['bp_3d_height']['height'].$modeview_3d['bp_3d_height']['unit'];
    }
    
    $camera_controls = $modeview_3d['bp_camera_control'] == 1 ? 'camera-controls' : '';
    $alt            = !empty($modeview_3d['bp_3d_src']['url']) ? $modeview_3d['bp_3d_src']['title'] : '';
    $auto_rotate    = $modeview_3d['bp_3d_rotate'] === '1' ? 'auto-rotate' : '';
    
    $zooming_3d     = $modeview_3d['bp_3d_zooming'] === '1' ? '' : 'disable-zoom';
    // Preload
    $loading   = isset ($modeview_3d['bp_3d_loading']) ? $modeview_3d['bp_3d_loading'] : '';

endif;
?>
<!-- 3D Model html -->
<div class="bp_grand wrapper_<?php echo esc_attr($id) ?>">   
<div class="bp_model_parent">
<model-viewer class="model" id="bp_model_id_<?php echo esc_attr($id); ?>" src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>" <?php echo esc_attr($camera_controls); ?> <?php echo esc_attr($zooming_3d); ?> loading="<?php  echo esc_attr($loading); ?>" <?php echo esc_attr($auto_rotate); ?> >
</model-viewer>

    <!-- Button -->
    <svg id="openBtn" width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#f2f2f2" class="bi bi-arrows-fullscreen model-icon">
        <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707zm0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707zm-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707z"/>
    </svg>

    <svg id="closeBtn" class="model-icon" width="34px" height="34px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path fill="none" stroke="#f2f2f2" stroke-width="2" d="M7,7 L17,17 M7,17 L17,7"/>
    </svg>
    <!-- ./Button -->

</div>
</div> 
<!-- Model Viewer Style -->
<style>

<?php echo '.wrapper_'.esc_attr($id); ?> .bp_model_parent, <?php echo '#bp_model_id_'.esc_attr($id); ?> {
    width: <?php echo esc_attr($width); ?>;
    max-width: 100%;
    <?php if(bp3d_isset($modeview_3d, 'bp_3d_align', 'center') == 'end'){
        echo "margin-left: auto";
    } ?>
    <?php if(bp3d_isset($modeview_3d, 'bp_3d_align', 'center') == 'center'){
        echo "margin: auto";
    } ?>
}
<?php echo '#bp_model_id_'.esc_attr($id); ?> {
    height:<?php echo esc_attr($height); ?>;
    background-color: <?php echo esc_attr($modeview_3d['bp_model_bg']); ?>;
}
.fullscreen <?php echo "#bp_model_id_".esc_attr($id); ?>{
  height: 100%;
  width: 100%;
}
<?php echo esc_html(".wrapper_$id .bp_model_parent") ?>{
    justify-content: <?php echo esc_attr(isset($modeview_3d['bp_3d_align']) ? $modeview_3d['bp_3d_align'] : 'center'); ?>
}
model-viewer.model {
    --poster-color: transparent;
}
</style>

<?php  

$output = ob_get_clean(); return $output; 
}
add_shortcode('3d_viewer','bp3dviewer_cpt_content_func');
