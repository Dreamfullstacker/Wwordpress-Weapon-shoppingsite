<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.


//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$prefix = '_bp3dimages_';

//
// Create a metabox
//
CSF::createMetabox( $prefix, array(
  'title'        => '3D Viewer Settings',
  'post_type'    => 'bp3d-model-viewer',
  'show_restore' => true,
) );


//
// section: 3D Model Viewer Pro
//
CSF::createSection( $prefix, array(
  'fields' => array(

    // 3D Model Options
    array(
      'id'       => 'bp_3d_model_type',
      'type'     => 'button_set',
      'title'    => __('Model Type.', 'model-viewer'),
      'subtitle' => __('Choose Model Type', 'model-viewer'),
      'desc'     => __('Select Model Type, Default- Simple.', 'model-viewer'),
      'multiple' => false,
      'options'  => array(
        'msimple'  => 'simple',
        'mcycle'   => 'Cycle',
      ),
      'default'  => array('msimple')
    ),
    array(
      'id'       => 'bp_3d_src_type',
      'type'     => 'button_set',
      'title'    => __('Model Source Type.', 'model-viewer'),
      'subtitle' => __('Choose Model Source', 'model-viewer'),
      'desc'     => __('Select Model Source, Default- Upload.', 'model-viewer'),
      'multiple' => false,
      'options'  => array(
        'upload'  => 'Upload',
        'link'   => 'Link',
      ),
      'default'  => array('upload'),
      'dependency' => array( 'bp_3d_model_type', '==', 'msimple'),
    ),
    array(
      'id'           => 'bp_3d_src',
      'type'         => 'media',
      'button_title' => __('Upload Source', 'model-viewer'),
      'title'        => __('3D Source', 'model-viewer'),
      'subtitle'     => __('Choose 3D Model', 'model-viewer'),
      'desc'         => __('Upload or Select 3d object files. Supported file type: glb, glTF', 'model-viewer'),
      'dependency' => array( 'bp_3d_model_type|bp_3d_src_type', '==|==', 'msimple|upload', 'all' ),
    ),
    array(
      'id'           => 'bp_3d_src_link',
      'type'         => 'text',
      'button_title' => __('Paste Source', 'model-viewer'),
      'title'        => __('3D Source', 'model-viewer'),
      'subtitle'     => __('Input Model Valid url', 'model-viewer'),
      'desc'         => __('Input / Paste Model url. Supported file type: glb, glTF', 'model-viewer'),
      'placeholder'  => 'Paste here Model url',
      'dependency' => array( 'bp_3d_model_type|bp_3d_src_type', '==|==', 'msimple|link', 'all' ),
      'class'    => 'bp3d-readonly'
    ),
    array(
      'id'     => 'bp_3d_models',
      'type'   => 'repeater',
      'title'        => __('3D Cycle Models', 'model-viewer'),
      'subtitle'     => __('Cycling between 3D Models', 'model-viewer'),
      'button_title' => __('Add New Model', 'model-viewer'),
      'desc'         => __('Use Multiple Model in a row.', 'model-viewer'),
      'class'    => 'bp3d-readonly',
      'fields' => array(
        array(
          'id'    => 'model_src',
          'type'  => 'media',
          'title' =>  __('Model Source', 'model-viewer'),
          'desc'  => __('Upload or Select 3d object files. Supported file type: glb, glTF', 'model-viewer'),
        ),
    
      ),
      'dependency' => array( 'bp_3d_model_type', '==', 'mcycle' ),
    ),
    array(
      'id'           => 'bp_3d_width',
      'type'         => 'dimensions',
      'title'        => __('Width', 'model-viewer'),
      'desc'         => __('3D Viewer Width', 'model-viewer'),
      'default'  => array(
        'width'  => '100',
        'unit'   => '%',
      ),
      'height'   => false,
    ),
    array(
      'id'           => 'bp_3d_height',
      'type'         => 'dimensions',
      'title'        => __('Height', 'model-viewer'),
      'desc'         => __('3D Viewer height', 'model-viewer'),
      'units'        => ['px', 'em', 'pt'],
      'default'  => array(
        'height' => '320',
        'unit'   => 'px',
      ),
      'width'   => false,
    ),
    array(
      'id'           => 'bp_model_bg',
      'type'         => 'color',
      'title'        => __('Background Color', 'model-viewer'),
      'subtitle'        => __('Set Background Color For 3d Model.If You don\'t need just leave blank. Default : \'transparent color\'', 'model-viewer'),
      'desc'         => __('Choose Your Background Color For Model.', 'model-viewer'),
      'default'      => 'transparent'
    ),
    array(
      'id'       => 'bp_camera_control',
      'type'     => 'switcher',
      'title'    => __('Moving Controls', 'model-viewer'),
      'desc'     => __('Use The Moving controls to enable user interaction', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default' => true,

    ),
    array(
      'id'        => 'bp_3d_zooming',
      'type'      => 'switcher',
      'title'     => 'Enable Zoom',
      'subtitle'  => __('Enable or Disable Zooming Behaviour', 'model-viewer'),
      'desc'      => __('If you wish to disable zooming behaviour please choose Yes.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => true,
    ),

    array(
      'id'         => 'bp_3d_loading',
      'type'       => 'radio',
      'title'      => __('Loading Type', 'model-viewer'),
      'subtitle'   => __('Choose Loading type, default:  \'Auto\' ', 'model-viewer'),
      'options'    => array(
        'auto'  => 'Auto',
        'lazy'  => 'Lazy',
        'eager' => 'Eager',
      ),
      'default'    => 'auto',
    ),
    array(
      'id' => 'bp_3d_align',
      'title' => __("Align", "model-viewer"),
      'type' => 'button_set',
      'options' => [
        'start' => 'Left',
        'center' => 'Center',
        'end' => 'Right'
      ],
      'default' => 'center',
    ),
    array(
      'id'        => 'bp_model_angle',
      'type'      => 'switcher',
      'title'     => 'Custom Angle',
      'subtitle'  => __('Specified Custom Angle of Model in Initial Load.', 'model-viewer'),
      'desc'      => __('Enable or Disable Custom Angle Option.', 'model-viewer'),
      'class'    => 'bp3d-readonly',
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => false,
    ),
    array(
      'id'    => 'angle_property',
      'type'  => 'spacing',
      'title' => 'Custom Angle Values',
      'subtitle'=> __('Set The Custom values for Model. Default Values are ("X=0deg Y=75deg Z=105%")', 'model-viewer'),
      'desc'    => __('Set Your Desire Values. (X= Horizontal Position, Y= Vertical Position, Z= Zoom Level/Position) ', 'model-viewer'),
      'default'  => array(
        'top'    => '0',
        'right'  => '75',
        'bottom' => '105',
      ),
      'left'   => false,
      'show_units' => false,
      'top_icon'    => 'Deg',
      'right_icon'  => 'Deg',
      'bottom_icon' => '%',
      'dependency' => array( 'bp_model_angle', '==', '1' ),
    ),
    array(
      'id'       => 'bp_3d_autoplay',
      'type'     => 'switcher',
      'title'    => __('Autoplay', 'model-viewer'),
      'subtitle' => __('Enable or Disable AutoPlay', 'model-viewer'),
      'desc'     => __('Autoplay Feature is for Autoplay Supported Model.', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default'  => false,
      'class'    => 'bp3d-readonly',
    ),
    array(
      'id'       => '3d_shadow_intensity',
      'type'     => 'spinner',
      'title'    => __('shadow Intensity', 'model-viewer'),
      'subtitle' => __('Shadow Intensity for Model', 'model-viewer'),
      'desc'     => __('Use Shadow Intensity Limit for Model. "1" for Default.', 'model-viewer'),
      'class'    => 'bp3d-readonly',
      'default' => '1',
    ),
    array(
      'id'           => 'bp_model_anim_du',
      'type'         => 'text',
      'title'        => __('Cycle Animation Duration', 'model-viewer'),
      'subtitle'     => __('Animation Duration Time at Seconds : 1000ms = 1sec', 'model-viewer'),
      'desc'         => __('Input Model Animation Duration Time (default: \'5\') Seconds', 'model-viewer'),
      'class'    => 'bp3d-readonly',
      'default'   => 5000,
      'dependency' => array( 'bp_3d_model_type', '==', 'mcycle' ),
    ),
    // Poster Options
    array(
      'id'       => 'bp_3d_poster_type',
      'type'     => 'button_set',
      'title'    => __('Poster Type.', 'model-viewer'),
      'subtitle' => __('Choose Poster Type', 'model-viewer'),
      'desc'     => __('Select Poster Type, Default- Simple.', 'model-viewer'),
      'class'    => 'bp3d-readonly',
      'multiple' => false,
      'options'  => array(
        'simple'  => 'simple',
        'cycle'   => 'Cycle',
      ),
      'default'  => array('simple'),
    ),
    array(
      'id'           => 'bp_3d_poster',
      'type'         => 'media',
      'button_title' => __('Upload Poster', 'model-viewer'),
      'title'        => __('3D Poster Image', 'model-viewer'),
      'subtitle'     => __('Display a poster until loaded', 'model-viewer'),
      'desc'         => __('Upload or Select 3d Poster Image.  if you don\'t want to use just leave it empty', 'model-viewer'),
      'class'    => 'bp3d-readonly',
      'dependency' => array( 'bp_3d_poster_type', '==', 'simple' ),
    ),
    array(
      'id'     => 'bp_3d_posters',
      'type'   => 'repeater',
      'title'        => __('Poster Images', 'model-viewer'),
      'subtitle'     => __('Cycling between posters', 'model-viewer'),
      'button_title' => __('Add New Poster Images', 'model-viewer'),
      'desc'         => __('Use multiple images for poster image.if you don\'t want to use just leave it empty', 'model-viewer'),
      'fields' => array(
        array(
          'id'    => 'poster_img',
          'type'  => 'upload',
          'title' => 'Poster Image'
        ),
    
      ),
      'dependency' => array( 'bp_3d_poster_type', '==', 'cycle' ),
      'class'    => 'bp3d-readonly',
    ),
    array(
      'id'        => 'bp_3d_preloader',
      'type'      => 'switcher',
      'title'     => 'Preload',
      'subtitle'  => __('Preload with poster and show model on interaction', 'model-viewer'),
      'desc'      => __('Choose "Yes" if you want to use preload with poster image.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'class'    => 'bp3d-readonly',
      'default'   => false,
    ),
    array(
      'id'        => 'bp_3d_progressbar',
      'type'      => 'switcher',
      'title'     => 'Progressbar',
      'subtitle'  => __('Enable or Disable Progressbar', 'model-viewer'),
      'desc'      => __('If you wish to disable Progressbar please choose No.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => true,
      'class'    => 'bp3d-readonly',
    ),
    array(
      'id'       => 'bp_3d_rotate',
      'type'     => 'switcher',
      'title'    => __('Auto Rotate', 'model-viewer'),
      'subtitle' => __('Enable or Disable Auto Rotation', 'model-viewer'),
      'desc'     => __('Enables the auto-rotation of the model.', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'class'    => 'bp3d-readonly',
      'default'  => true,
    ),
    array(
      'id'       => '3d_rotate_speed',
      'type'     => 'spinner',
      'title'    => __('Auto Rotate Speed', 'model-viewer'),
      'subtitle' => __('Auto Rotation Speed Per Seconds', 'model-viewer'),
      'desc'     => __('Use Negative Number for Reverse Action. "30" for Default Behaviour.', 'model-viewer'),
      'min'         => 0,
      'max'         => 180,
      'default' => 30,
      'class'    => 'bp3d-readonly',
      'dependency' => array( 'bp_3d_rotate', '==', true ),
    ),
    array(
      'id'       => '3d_rotate_delay',
      'type'     => 'number',
      'title'    => __('Auto Rotation Delay', 'model-viewer'),
      'subtitle' => __('After a period of time auto rotation will start', 'model-viewer'),
      'desc'     => __('Sets the delay before auto-rotation begins. The format of the value is a number in milliseconds.(1000ms = 1s)', 'model-viewer'),
      'default' => 3000,
      'class'    => 'bp3d-readonly',
      'dependency' => array( 'bp_3d_rotate', '==', true ),
    ),
    array(
      'id'       => 'bp_3d_fullscreen',
      'type'     => 'switcher',
      'title'    => __('Fullscreen', 'model-viewer'),
      'subtitle' => __('Enable or Disable Fullscreen Mode', 'model-viewer'),
     'desc'     => __('Default: "Yes / Enable"', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'class'    => 'bp3d-readonly',
      'default'  => true,
    ),
    
  ) // End fields


) );


/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function bp3dviewer_readonly() {
  wp_register_style( 'bp3d-readonly', plugin_dir_url( __FILE__ ) . '../public/css/readonly.css', false, '1.0' );
  echo wp_enqueue_style( 'bp3d-readonly' );
}
add_action( 'admin_enqueue_scripts', 'bp3dviewer_readonly' );



function bp3dviewer_exclude_fields_before_save( $data ) {

  $exclude = array(
    'bp_3d_models',
    'bp_3d_src_link',
    'bp_model_anim_du',
    'bp_3d_poster_type',
    'bp_3d_poster',
    'bp_3d_posters',
    'bp_3d_progressbar',
    'bp_3d_preloader',
    'bp_3d_rotate',
    '3d_rotate_speed',
    '3d_rotate_delay',
    'bp_3d_autoplay',
    '3d_shadow_intensity',
  );
  
  foreach ( $exclude as $id ) {
  unset( $data[$id] );
  }
  
  return $data;
  
  }
  add_filter( 'csf_sc__save', 'bp3dviewer_exclude_fields_before_save', 10, 1 );