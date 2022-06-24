<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$prefix = '_bp3d_settings_';

//
// Create a Option
//
CSF::createOptions( $prefix, array(
  'menu_title'  => 'Settings',
  'menu_slug'   => '3dviewer-settings',
  'menu_type'   => 'submenu',
  'menu_parent' => 'edit.php?post_type=bp3d-model-viewer',
  'theme'       => 'light',
  'framework_title' => '3D Viewer Settings',
  'menu_position' => 10,
  'footer'      => false,
  'footer_credit'  => '3D Viewer',
  'footer_text'             => '',

) );


// Preset Settings for Add New 
CSF::createSection( $prefix, array(
  'title'  => 'Preset',
  'class'    => 'bp3d-readonly',
  'fields' => array(

    array(
      'id'           => 'bpp_3d_width',
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
      'id'      => 'bpp_3d_height',
      'type'    => 'dimensions',
      'title'   => __('Height', 'model-viewer'),
      'desc'    => __('3D Viewer height', 'model-viewer'),
      'units'   => ['px', 'em', 'pt'],
      'default'  => array(
        'height' => '320',
        'unit'   => 'px',
      ),
      'width'   => false,
    ),
    array(
      'id'           => 'bpp_model_bg',
      'type'         => 'color',
      'title'        => __('Background Color', 'model-viewer'),
      'subtitle'        => __('Set Background Color For 3d Model.If You don\'t need just leave blank. Default : \'transparent color\'', 'model-viewer'),
      'desc'         => __('Choose Your Background Color For Model.', 'model-viewer'),
      'default'      => 'transparent'
    ),
    array(
      'id'       => 'bpp_3d_autoplay',
      'type'     => 'switcher',
      'title'    => __('Autoplay', 'model-viewer'),
      'subtitle' => __('Enable or Disable AutoPlay', 'model-viewer'),
      'desc'     => __('Autoplay Feature is for Autoplay Supported Model.', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default'  => false,
    ),
    array(
      'id'       => '3dp_shadow_intensity',
      'type'     => 'spinner',
      'title'    => __('Shadow Intensity', 'model-viewer'),
      'subtitle' => __('Shadow Intensity for Model', 'model-viewer'),
      'desc'     => __('Use Shadow Intensity Limit for Model. "1" for Default.', 'model-viewer'),
      'default' => '1',
    ),

    array(
      'id'        => 'bpp_3d_preloader',
      'type'      => 'switcher',
      'title'     => 'Preload',
      'subtitle'  => __('Preload with poster and show model on interaction', 'model-viewer'),
      'desc'      => __('Choose "Yes" if you want to use preload with poster image.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => false,
    ),
    array(
      'id'       => 'bpp_camera_control',
      'type'     => 'switcher',
      'title'    => __('Moving Controls', 'model-viewer'),
      'desc'     => __('Use The Moving controls to enable user interaction', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default' => true,

    ),
    array(
      'id'        => 'bpp_3d_zooming',
      'type'      => 'switcher',
      'title'     => 'Enable Zoom',
      'subtitle'  => __('Enable or Disable Zoom Behaviour', 'model-viewer'),
      'desc'      => __('If you wish to disable zooming behaviour please choose No.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => true,
    ),
    array(
      'id'        => 'bpp_3d_progressbar',
      'type'      => 'switcher',
      'title'     => 'Progressbar',
      'subtitle'  => __('Enable or Disable Progressbar', 'model-viewer'),
      'desc'      => __('If you wish to disable Progressbar please choose No.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => true,
    ),
    array(
      'id'         => 'bpp_3d_loading',
      'type'       => 'radio',
      'title'      => __('Loading Type', 'model-viewer'),
      'subtitle'   => __('Choose Loading type, default:  \'Auto\' ', 'model-viewer'),
      'options'    => array(
        'auto'  => 'Auto',
        'lazy'  => 'Lazy',
        'eager' => 'Eager',
      ),
      'default' => 'auto',
    ),

    array(
      'id'       => 'bpp_3d_rotate',
      'type'     => 'switcher',
      'title'    => __('Auto Rotate', 'model-viewer'),
      'subtitle' => __('Enable or Disable Auto Rotation', 'model-viewer'),
      'desc'     => __('Enables the auto-rotation of the model.', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default'  => true,

    ),
    array(
      'id'       => '3dp_rotate_speed',
      'type'     => 'spinner',
      'title'    => __('Auto Rotate Speed', 'model-viewer'),
      'subtitle' => __('Auto Rotation Speed Per Seconds', 'model-viewer'),
      'desc'     => __('Use Negative Number for Reverse Action. "30" for Default Behaviour.', 'model-viewer'),
      'min'         => 0,
      'max'         => 180,
      'default' => 30,
      'dependency' => array( 'bp_3d_rotate', '==', true ),
    ),
    array(
      'id'       => '3dp_rotate_delay',
      'type'     => 'number',
      'title'    => __('Auto Rotation Delay', 'model-viewer'),
      'subtitle' => __('After a period of time auto rotation will start', 'model-viewer'),
      'desc'     => __('Sets the delay before auto-rotation begins. The format of the value is a number in milliseconds.(1000ms = 1s)', 'model-viewer'),
      'default' => 3000,
      'dependency' => array( 'bp_3d_rotate', '==', true ),
    ),
    array(
      'id'       => 'bpp_3d_fullscreen',
      'type'     => 'switcher',
      'title'    => __('Fullscreen', 'model-viewer'),
      'subtitle' => __('Enable or Disable Fullscreen Mode', 'model-viewer'),
     'desc'     => __('Default: "Yes / Enable"', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default'  => true,
    ),
  ) // End fields


) );



// Woocommerce Settings
CSF::createSection( $prefix, array(
  'title'  => 'Woocommerce Settings',
  'fields' => array(

    // 3D Model Options
    array(
      'id'       => '3d_woo_switcher',
      'type'      => 'switcher',
      'title'    => __('Woocommerce', 'model-viewer'),
      'subtitle' => __('Enable / Disable Woocommerce Feature for 3D Viewer.', 'model-viewer'),
      'desc'     => __('Enable / Disable. Default is Enable.', 'model-viewer'),
      'default' => true,
    ),
    array(
      'id'       => '3d_shadow_intensity',
      'type'     => 'spinner',
      'title'    => __('Shadow Intensity', 'model-viewer'),
      'subtitle' => __('Shadow Intensity for Model', 'model-viewer'),
      'desc'     => __('Use Shadow Intensity Limit for Model. "1" for Default.', 'model-viewer'),
      'default' => '1',
      'class'    => 'bp3d-readonly'
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
      'subtitle'  => __('Enable or Disable Zoom Behaviour', 'model-viewer'),
      'desc'      => __('If you wish to disable zooming behaviour please choose No.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => true,
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
      'class'    => 'bp3d-readonly'
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
      'id'       => 'bp_3d_rotate',
      'type'     => 'switcher',
      'title'    => __('Auto Rotate', 'model-viewer'),
      'subtitle' => __('Enable or Disable Auto Rotation', 'model-viewer'),
      'desc'     => __('Enables the auto-rotation of the model.', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default'  => true,
      'class'    => 'bp3d-readonly'
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
      'dependency' => array( 'bp_3d_rotate', '==', true ),
      'class'    => 'bp3d-readonly'
    ),
    array(
      'id'       => '3d_rotate_delay',
      'type'     => 'number',
      'title'    => __('Auto Rotation Delay', 'model-viewer'),
      'subtitle' => __('After a period of time auto rotation will start', 'model-viewer'),
      'desc'     => __('Sets the delay before auto-rotation begins. The format of the value is a number in milliseconds.(1000ms = 1s)', 'model-viewer'),
      'default' => 3000,
      'dependency' => array( 'bp_3d_rotate', '==', true ),
      'class'    => 'bp3d-readonly'
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
      'class'    => 'bp3d-readonly'
    ),
    array(
      'id'       => 'bp_3d_fullscreen',
      'type'     => 'switcher',
      'title'    => __('Fullscreen', 'model-viewer'),
      'subtitle' => __('Enable or Disable Fullscreen Mode', 'model-viewer'),
     'desc'     => __('Default: "Yes / Enable"', 'model-viewer'),
      'text_on'  => 'Yes',
      'text_off' => 'No',
      'default'  => true,
      'class'    => 'bp3d-readonly'
    ),
  ) // End fields


) );
