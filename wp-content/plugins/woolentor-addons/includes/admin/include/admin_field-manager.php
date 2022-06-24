<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woolentor_Admin_Fields_Manager {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Woolentor_Admin_Fields_Manager]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init(){
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
    }

    /**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery' );
    }

    public function add_field( $option, $section ){

        $name       = isset( $option['option_id'] ) ? $option['option_id'] : $option['name'];
        $value      = isset( $option['value'] ) ? $option['value'] : '';
        $type       = isset( $option['type'] ) ? $option['type'] : 'text';
        $label      = isset( $option['label'] ) ? $option['label'] : '';
        $preview    = isset( $option['preview'] ) ? $option['preview'] : '';
        $section    = isset( $option['section'] ) ? $option['section'] : $section;
        $documentation     = isset( $option['documentation'] ) ? $option['documentation'] : '';
        $require_settings  = isset( $option['require_settings'] ) ? $option['require_settings'] : '';
        $setting_fields    = isset( $option['setting_fields'] ) ? $option['setting_fields'] : '';
        $is_pro            = isset( $option['is_pro'] ) ? $option['is_pro'] : '';
        $fields            = isset( $option['fields'] ) ? $option['fields'] : [];
        $callback          = isset( $option['callback'] ) ? $option['callback'] : [ $this, 'callback_' . $type ];

        $args = array(
            'id'                => $name,
            'class'             => isset( $option['class'] ) ? $option['class'] : $name,
            'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
            'name'              => $label,
            'value'             => $value,
            'title_field'       => isset( $option['title_field'] ) ? $option['title_field'] : '',
            'section'           => $section,
            'size'              => isset( $option['size'] ) ? $option['size'] : null,
            'options'           => isset( $option['options'] ) ? $option['options'] : '',
            'std'               => isset( $option['default'] ) ? $option['default'] : '',
            'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
            'type'              => $type,
            'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
            'min'               => isset( $option['min'] ) ? $option['min'] : '',
            'max'               => isset( $option['max'] ) ? $option['max'] : '',
            'step'              => isset( $option['step'] ) ? $option['step'] : '',
            'headding'          => isset( $option['headding'] ) ? $option['headding'] : '',
            'fields'            => $fields,
            'additional_info'   => [
                'preview'           => $preview,
                'documentation'     => $documentation,
                'require_settings'  => $require_settings,
                'setting_fields'    => $setting_fields,
                'is_pro'            => $is_pro
            ]
        );

        $this->create_field( $args, $callback );

    }

    public function create_field( $args, $callback ){
        call_user_func( $callback, $args );
    }

    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_field_title( $args ) {

        if ( ! empty( $args['name'] ) ) {
            $probadge = '';
            if( $args['additional_info']['is_pro'] === true ){
                $probadge = '<span class="woolentor-admin-switch-block-badge">'.esc_html__( 'Pro', 'woolentor' ).'</span>';
            }
            $desc = sprintf( '<h6 class="woolentor-admin-option-title">%s%s</h6>', $args['name'], $probadge );
        } else {
            $desc = '';
        }
        return $desc;
    }

    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_field_description( $args ) {
        if ( ! empty( $args['desc'] ) ) {
            $desc = sprintf( '<p class="woolentor-admin-option-text">%s</p>', $args['desc'] );
        } else {
            $desc = '';
        }
        return $desc;
    }

    /**
     * Get Title for display
     *
     * @param array $args settings field args
     */
    public function callback_title( $args ) {
        $headding  = isset( $args['headding'] ) ? $args['headding'] : '';
        $size      = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $html      = sprintf( '<div class="woolentor-admin-option-heading %3$s"><h4 class="woolentor-admin-option-heading-title %1$s-title">%2$s</h4></div>', $size, $headding, $args['class'] );
        echo $html;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_text( $args ) {

        $value       = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).'">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html  .= '<div class="woolentor-admin-input">';
                    $html  .= sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%4$s" value="%5$s" %6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
                $html  .= '</div>';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_image_upload( $args ) {

        $value       = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $id          = $args['section']  . '[' . $args['id'] . ']';

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $label        = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose Image','woolentor' );
        $remove_label = isset( $args['options']['button_remove_label'] ) ? $args['options']['button_remove_label'] : __( 'Remove' );
        $save_file    = ( $value != '' ) ? '<img src="'.esc_url( $value ).'" alt="'.esc_attr__( $label, 'woolentor' ).'">' : '';

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).'">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html  .= '<div class="woolentor-admin-media-upload">';
                    $html .= '<div class="woolentor_display">'.$save_file.'</div>';
                    $html  .= sprintf( '<input type="hidden" class="%1$s-text woolentor-url" id="%2$s[%3$s]" name="%3$s" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
                    $html  .= '<input type="button" class="button woolentor-browse" value="' . $label . '" />';
                    $html  .= '<input type="button" class="button woolentor-remove" value="' . $remove_label . '" />';
                $html  .= '</div>';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
        
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_module( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        
        $probadge = $data_atr = '';
        if( $args['additional_info']['is_pro'] === true ){
            $probadge = '<span class="woolentor-admin-switch-block-badge">'.esc_html__( 'Pro', 'woolentor' ).'</span>';
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        if( $value === 'on' ){
            $args['class'] .= ' woolentor-module-enable';
        }

        $setting_fields = '';
        if( !empty( $args['additional_info']['setting_fields'] ) ){
            $setting_fields = wp_json_encode( $args['additional_info']['setting_fields'] );
        }

        $html  = '<div class="woolentor-admin-switch-block woolentor-module-field '.esc_attr( $args['class'] ).'">';
            $html .= '<div class="woolentor-admin-switch-block-content">';
                $html  .= sprintf('<h6 class="woolentor-admin-switch-block-title">%1$s</h6>', $args['name'] );
                $html  .= '<div class="woolentor-admin-switch-block-info">';
                    $html  .= !empty( $args['additional_info']['preview'] ) ? '<a href="'.$args['additional_info']['preview'].'" data-woolentor-tooltip="'.esc_attr__('Preview','woolentor').'"><i class="wli wli-monitor"></i></a>' : '';
                    $html  .= !empty( $args['additional_info']['documentation'] ) ? '<a href="'.$args['additional_info']['documentation'].'" data-woolentor-tooltip="'.esc_attr__('Documentation','woolentor').'"><i class="wli wli-question"></i></a>' : '';
                    $html .= $probadge;
                $html  .= '</div>';
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-switch-block-actions" '.$data_atr.'>';
            $html  .= !empty( $args['additional_info']['require_settings'] ) ? '<a href="#" class="woolentor-admin-switch-block-setting" data-section="'.$args['section'].'" data-fields=\'' .$setting_fields. '\'><i class="wli wli-cog-light"></i></a>' : '';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;

    }

    /**
     * Displays a repeater field for a settings field
     *
     * @param array $args settings field args
    */
    public function callback_repeater( $args ) {

        $values = $this->get_option( $args['id'], $args['section'], $args['std'] );
        echo '<div class="woolentor-admin-option woolentor-repeater-heading">';
            echo $this->get_field_title( $args );
            echo $this->get_field_description( $args );
        echo '</div>';
        $number = 0;
        if ( ! empty( $values ) && is_array( $values ) ) {        
            echo '<div class="woolentor-option-repeater-item-area">';
                foreach( $values as $key => $value ){
                    echo '<div class="woolentor-option-repeater-item" data-id="'.esc_attr( $number ).'">';

                    $title_field = isset( $values[$key][$args['title_field']] ) ? $values[$key][$args['title_field']] : '';
                    ?>
                        <div class="woolentor-option-repeater-tools">
                            <div class="woolentor-option-repeater-item-title">
                                <?php echo esc_html( $title_field ); ?>
                            </div>
                            <div class="woolentor-option-repeater-item-remove">
                                <span class="dashicon dashicons dashicons-no-alt">&nbsp;</span>
                            </div>
                        </div>
                        <div class="woolentor-option-repeater-fields">
                            <?php
                                foreach ( $args['fields'] as $field ) {
                                    $field['option_id']   = '['.$args['id'].'][]['.$field['name'].']';
                                    $field['class']       = $field['class'].' woolentor-repeater-field';
                                    $field['value']       = ( isset( $field['name'] ) && isset( $values[$key][$field['name']] ) ) ? $values[$key][$field['name']] : '';
                                    $this->add_field( $field, $args['section'] );
                                }
                            ?>
                        </div>
                    <?php
                    echo '</div>';
                    $number++;
                }
            echo '</div>';
        }else{
            echo '<div class="woolentor-option-repeater-item-area"><div class="woolentor-option-repeater-item" style="margin:0;height:0;">&nbsp;</div></div>';
        }

        echo '<div class="woolentor-option-repeater-item woolentor-repeater-hidden">';
        ?>
            <div class="woolentor-option-repeater-tools">
                <div class="woolentor-option-repeater-item-title">&nbsp;</div>
                <div class="woolentor-option-repeater-item-remove">
                    <span class="dashicon dashicons dashicons-no-alt">&nbsp;</span>
                </div>
            </div>
            <div class="woolentor-option-repeater-fields">
                <?php 
                    foreach( $args['fields'] as $field ){
                        $field['option_id'] = '['.$args['id'].'][]['.$field['name'].']';
                        $field['class']     = $field['class'].' woolentor-repeater-field';
                        $this->add_field( $field, $args['section'] );
                    }
                ?>
            </div>
        <?php
        echo '</div>';
        echo '<button type="button" class="woolentor-repeater-item-add woolentor-admin-btn-primary"><span class="dashicon dashicons dashicons-plus-alt2"></span>'.esc_html__('Add Item','woolentor').'</button>';


    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_element( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        
        $checked = checked( $value, 'on', false );
        $switch_id = esc_attr('data-switch-id=element');
        $probadge = $data_atr = '';
        if( $args['additional_info']['is_pro'] === true ){
            $probadge = '<span class="woolentor-admin-switch-block-badge">'.esc_html__( 'Pro', 'woolentor' ).'</span>';
            $checked = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
            $switch_id = '';
        }

        $setting_fields = '';
        if( !empty( $args['additional_info']['setting_fields'] ) ){
            $setting_fields = wp_json_encode( $args['additional_info']['setting_fields'] );
        }
        $visibility = 'woolentor-visibility-none';
        if( ( $args['additional_info']['require_settings'] === true ) && ( $value === 'on' ) ){
            $visibility = '';
        }

        $html  = '<div class="woolentor-admin-switch-block '.esc_attr( $args['class'] ).'">';
            $html .= '<div class="woolentor-admin-switch-block-content">';
                $html  .= sprintf('<h6 class="woolentor-admin-switch-block-title">%1$s</h6>', $args['name'] );
                $html  .= '<div class="woolentor-admin-switch-block-info">';
                    $html  .= !empty( $args['additional_info']['preview'] ) ? '<a href="'.$args['additional_info']['preview'].'" data-woolentor-tooltip="'.esc_attr__('Preview','woolentor').'"><i class="wli wli-monitor"></i></a>' : '';
                    $html  .= !empty( $args['additional_info']['documentation'] ) ? '<a href="'.$args['additional_info']['documentation'].'" data-woolentor-tooltip="'.esc_attr__('Documentation','woolentor').'"><i class="wli wli-question"></i></a>' : '';
                    $html .= $probadge;
                $html  .= '</div>';
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-switch-block-actions" '.$data_atr.'>';
            $html  .= !empty( $args['additional_info']['require_settings'] ) ? '<a href="#" class="woolentor-admin-switch-block-setting '.$visibility.'" data-section="'.$args['section'].'" data-fields=\'' .$setting_fields. '\'><i class="wli wli-cog-light"></i></a>' : '';
                $html  .= '<div class="woolentor-admin-switch" '.$switch_id.'>';
                        $html  .= sprintf( '<input type="checkbox" class="checkbox" id="woolentor_field_%1$s[%2$s]" name="%2$s" value="on" %3$s/>', $args['section'], $args['id'], $checked );
                        $html  .= sprintf( '<label for="woolentor_field_%1$s[%2$s]"><span class="woolentor-admin-switch-label on">%3$s</span><span class="woolentor-admin-switch-label off">%4$s</span><span class="woolentor-admin-switch-indicator"></span></label>', $args['section'], $args['id'], 'on', 'off' );
                    $html  .= '</div>';
                $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
    }

    /**
     * Displays a number field for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_number( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'number';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min         = ( $args['min'] == '' ) ? '' : ' min="' . $args['min'] . '"';
        $max         = ( $args['max'] == '' ) ? '' : ' max="' . $args['max'] . '"';
        $step        = ( $args['step'] == '' ) ? '' : ' step="' . $args['step'] . '"';

        $data_atr = $checked = '';
        if( $args['additional_info']['is_pro'] === true ){
            $checked = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).'">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html  .= '<div class="woolentor-admin-number">';
                    $html  .= sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%4$s" value="%5$s" %6$s%7$s%8$s%9$s%10$s />', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step, $checked );
                    $html  .= '<span class="woolentor-admin-number-btn increase">+</span>';
                    $html  .= '<span class="woolentor-admin-number-btn decrease">-</span>';
                $html  .= '</div>';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
    }

     /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_checkbox( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $checked = checked( $value, 'on', false );
        $data_atr = '';
        if( $args['additional_info']['is_pro'] === true ){
            $checked = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).'">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html  .= '<div class="woolentor-admin-switch">';
                    $html  .= sprintf( '<input type="checkbox" class="checkbox" id="woolentor_field_%1$s[%2$s]" name="%2$s" value="on" %3$s/>', $args['section'], $args['id'], $checked );
                    $html  .= sprintf( '<label for="woolentor_field_%1$s[%2$s]"><span class="woolentor-admin-switch-label on">%3$s</span><span class="woolentor-admin-switch-label off">%4$s</span><span class="woolentor-admin-switch-indicator"></span></label>', $args['section'], $args['id'], 'on', 'off' );
                $html  .= '</div>';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
    }

    /**
     * Displays a radio button for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_radio( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? $args['value'] : $this->get_option( $args['id'], $args['section'], $args['std'] );

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).' ">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';

                foreach ( $args['options'] as $key => $label ) {
                    $html  .= '<div class="woolentor-admin-radio">';
                        $html .= sprintf( '<input type="radio" class="radio" id="woolentor_field_%1$s[%2$s][%3$s]" name="%2$s" value="%3$s" %4$s %5$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ), $disabled );
                        $html .= sprintf( '<label for="woolentor_field_%1$s[%2$s][%3$s]">%4$s</label>',  $args['section'], $args['id'], $key, $label );
                    $html  .= '</div>';
                }

            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;

    }
    

    /**
     * Displays a selectbox for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_select( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).' ">';
            $html .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html .= '</div>';
            $html .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html .= '<div class="woolentor-admin-select">';
                    $html  .= sprintf( '<select class="%1$s" name="%3$s" id="%2$s[%3$s]" %4$s>', $size, $args['section'], $args['id'], $disabled );
                        foreach ( $args['options'] as $key => $label ) {
                            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
                        }
                    $html .= sprintf( '</select>' );
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        echo $html;
    }

    /**
     * Displays a selectgroup for a settings field
     *
     * @param array $args settings field args
     */
    public function callback_selectgroup( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).' ">';
            $html .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html .= '</div>';
            $html .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html .= '<div class="woolentor-admin-select">';
                    $html  .= sprintf( '<select class="%1$s" name="%3$s" id="%2$s[%3$s]" %4$s>', $size, $args['section'], $args['id'], $disabled );
                        
                        foreach( $args['options']['group'] as $key => $label ){

                            if( !empty( $args['options']['group'][$key]['options'] ) ){
                                $html .= '<optgroup label="'.$args['options']['group'][$key]['label'].'">';
                                    foreach ( $args['options']['group'][$key]['options'] as $optionkey => $optionlabel ) {
                                        $html .= sprintf( '<option value="%s"%s>%s</option>', $optionkey, selected( $value, $optionkey, false ), $optionlabel );
                                    }
                                $html .= '</optgroup>';
                            }

                        }

                    $html .= sprintf( '</select>' );
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        echo $html;

    }

    /**
     * Displays a multiselect for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_multiselect( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? $args['value'] : $this->get_option( $args['id'], $args['section'], $args['std'] );

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }
        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).' ">';
            $html .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html .= '</div>';
            $html .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html .= '<div class="woolentor-admin-select">';
                    $html .= sprintf( '<select multiple="multiple" class="%1$s" name="%2$s[]" id="%1$s[%2$s]" %3$s>', $args['section'], $args['id'], $disabled );
                        foreach ( $args['options'] as $key => $label ) {
                            $selected = '';
                            if( !empty( $value ) ){
                                $selected = ( is_array( $value ) && in_array( $key, $value ) ) ? $key : '';
                            }
                            $html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', $key, selected( $selected, $key, false ), $label );
                        }
                    $html .= sprintf( '</select>' );
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        echo $html;

    }

    /**
     * Displays a color picker field for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_color( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).' ">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html  .= '<div class="woolentor-admin-color">';
                    $html  .= sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%3$s" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
                $html  .= '</div>';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
    }

    /**
     * Displays a DIMENSIONS for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_dimensions( $args ) {

        $value = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? $args['value'] : $this->get_option( $args['id'], $args['section'], $args['std'] );

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }
        
        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).' ">';

            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';

            $html .= '<div class="woolentor-admin-option-action" '.$data_atr.'><ul class="woolentor_dimensions">';
                foreach ( $args['options'] as $key => $label ) {
                    $new_value = isset( $value[$key] ) ? $value[$key] : '';
                    $html .= '<li>';
                        if( 'unit' === $key ){
                            $html    .= sprintf( '<input type="text" class="dimensionsbox" id="woolentor_sp_%1$s[%2$s][%3$s]" name="%2$s[%3$s]" value="%4$s" />', $args['section'], $args['id'], $key, $new_value );
                            $html    .= sprintf( '<label for="woolentor_sp_%1$s[%2$s][%3$s]">%4$s</label>', $args['section'], $args['id'], $key, $label );
                        }else{
                            $html    .= sprintf( '<input type="number" class="dimensionsbox" id="woolentor_sp_%1$s[%2$s][%3$s]" name="%2$s[%3$s]" value="%4$s" />', $args['section'], $args['id'], $key, $new_value );
                            $html    .= sprintf( '<label for="woolentor_sp_%1$s[%2$s][%3$s]">%4$s</label>', $args['section'], $args['id'], $key, $label );
                        }
                    $html .= '</li>';
                }
            $html .= '</ul></div>';

        $html .= '</div>';

        echo $html;
    }

    /**
     * Displays a date picker field for a settings field
     *
     * @param array   $args settings field args
     */
    public function callback_date( $args ) {

        $value       = ( isset( $args['value'] ) && !empty ( $args['value'] ) ) ? esc_attr( $args['value'] ) : esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

        $data_atr = $disabled = '';
        if( $args['additional_info']['is_pro'] === true ){
            $disabled = esc_attr('disabled=true');
            $data_atr = esc_attr( 'data-woolentor-pro=disabled' );
        }

        $html  = '<div class="woolentor-admin-option '.esc_attr( $args['class'] ).'">';
            $html  .= '<div class="woolentor-admin-option-content">';
                $html  .= $this->get_field_title( $args );
                $html  .= $this->get_field_description( $args );
            $html  .= '</div>';
            $html  .= '<div class="woolentor-admin-option-action" '.$data_atr.'>';
                $html  .= '<div class="woolentor-admin-input">';
                    $html  .= sprintf( '<input type="%1$s" class="%2$s-text woolentor-date-picker-field" id="%3$s[%4$s]" name="%4$s" value="%5$s" %6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
                $html  .= '</div>';
            $html  .= '</div>';
        $html  .= '</div>';

        echo $html;
    }

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    public function get_option( $option, $section, $default = '' ) {
        $options = get_option( $section );
        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }
        return $default;
    }

    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    public function script() {
        ?>
        <script>
            ;jQuery(document).ready(function($) {
                
                //Initiate Color Picker
                $('.wp-color-picker-field').wpColorPicker({

                    change: function (event, ui) {
                        $(this).closest('.woolentor-admin-main-tab-pane').find('.woolentor-admin-btn-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
                    },

                    clear: function (event) {
                        $(this).closest('.woolentor-admin-main-tab-pane').find('.woolentor-admin-btn-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
                    }
                    
                });

                $('div[data-woolentor-pro="disabled"] .wp-picker-container button').each(function(){
                    $(this).attr("disabled", true);
                });

                // Icon Picker
                $('.woolentor_icon_picker .regular-text').fontIconPicker({
                    source: woolentor_fields.iconset,
                    emptyIcon: true,
                    hasSearch: true,
                    theme: 'fip-bootstrap'
                }).on('change', function() {
                    $(this).closest('.woolentor-admin-main-tab-pane').find('.woolentor-admin-btn-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
                });

                // Media Uploader
                $('.woolentor-browse').on('click', function (event) {
                    event.preventDefault();

                    var self = $(this);

                    // Create the media frame.
                    var file_frame = wp.media.frames.file_frame = wp.media({
                        title: self.data('uploader_title'),
                        button: {
                            text: self.data('uploader_button_text'),
                        },
                        multiple: false
                    });

                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();
                        self.prev('.woolentor-url').val(attachment.url).change();
                        self.siblings('.woolentor_display').html('<img src="'+attachment.url+'" alt="'+attachment.title+'" />');
                    });

                    // Finally, open the modal
                    file_frame.open();
                    
                });

                // Remove Media Button
                $('.woolentor-remove').on('click', function (event) {
                    event.preventDefault();
                    var self = $(this);
                    self.siblings('.woolentor-url').val('').change();
                    self.siblings('.woolentor_display').html('');
                });

                // Initiate sortable Field
                $( ".woolentor-option-repeater-item-area" ).sortable({
                    axis: 'y',
                    connectWith: ".woolentor-option-repeater-item",
                    handle: ".woolentor-option-repeater-tools",
                    placeholder: "widget-placeholder",
                    update: function( event, ui ) {
                        $('.woolentor-admin-module-save').removeClass('disabled').attr('disabled', false).text( WOOLENTOR_ADMIN.message.btntxt );
                    }
                });

            });
        </script>
        <?php
    }

    

}