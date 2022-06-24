<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$prefix = '_bp3d_product_';

//
// Create a metabox
//
CSF::createMetabox( $prefix, array(
  'title'        => '3D Viewer Settings',
  'post_type'    =>  'product',
  'show_restore' => true,
) );

// Section for 3D Viewer
CSF::createSection( $prefix, array(
  'fields' => array(

    // 3D Model Options
    array(
      'id'     => 'bp3d_models',
      //'type'   => 'group',
      'type'   => 'repeater',
      'title'  => 'Product 3D Models',
      'desc'  => 'Click on + icon to add 3d files, if you add multiple 3d files, we will show them as a slider <cite Style="color:#2271b1; font-weight: bold ">Multiple Files Support Only For Pro Version</cite>',
      //'button_title' => __('Add New Model', 'model-viewer'),
      'max'   => 1,
      'fields' => array(
        array(
          'id'           => 'model_src',
          'type'         => 'upload',
          'title'        => __('3D Source', 'model-viewer'),
          'subtitle'     => __('Upload Model Or Input Valid Model url', 'model-viewer'),
          'desc'         => __('Upload / Paste Model url. Supported file type: glb, glTF', 'model-viewer'),
          'placeholder'  => 'You Can Paste here Model url',
        ),
      ),
    ),
    // Model Positioning Option
    array(
      'id'         => 'viewer_position',
      'type'       => 'radio',
      'title'      => '3D Viewer Position',
      'options'    => array(
        'top' => 'Top of the product image',
        'bottom' => 'Bottom of the product image',
        'replace' => 'Replace Product Image with 3D',
      ),
      'default'    => 'top'
    ),

    array(
      'id'        => 'bp_model_angle',
      'type'      => 'switcher',
      'title'     => 'Custom Angle',
      'subtitle'  => __('Specified Custom Angle of Model in Initial Load.', 'model-viewer'),
      'desc'      => __('Enable or Disable Custom Angle Option.', 'model-viewer'),
      'text_on'   => 'Yes',
      'text_off'  => 'NO',
      'text_width'  => 60,
      'default'   => false,
      'class'    => 'bp3d-readonly'
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

  ) // End fields


) );
