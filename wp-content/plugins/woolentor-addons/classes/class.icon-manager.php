<?php

namespace Elementor;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;

/**
* Icon render
*/
class WooLentor_Icon_manager extends Icons_Manager{

    private static function render_svg_icon( $value ) {
        if ( ! isset( $value['id'] ) ) {
            return '';
        }
        return Svg_Handler::get_inline_svg( $value['id'] );
    }

    private static function render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
        $icon_types = self::get_icon_manager_tabs();
        if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
            return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
        }

        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $icon['value'];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $icon['value'];
            } else {
                $attributes['class'] .= ' ' . $icon['value'];
            }
        }
        return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
    }

    public static function render_icon( $icon, $attributes = [], $tag = 'i' ) {
        if ( empty( $icon['library'] ) ) {
            return false;
        }
        $output = '';
        // handler SVG Icon
        if ( 'svg' === $icon['library'] ) {
            $output = self::render_svg_icon( $icon['value'] );
        } else {
            $output = self::render_icon_html( $icon, $attributes, $tag );
        }
        return $output;
    }

}

/**
 * [woolentor_addons_render_icon]
 * @param  array  $settings 
 * @param  string $new_icon  new icon id
 * @param  string $old_icon  Old icon id
 * @param  array  $attributes icon attributes
 * @return [html]  html | false
 */
function woolentor_render_icon( $settings = [], $new_icon = 'selected_icon', $old_icon = 'icon', $attributes = [] ){

    $migrated = isset( $settings['__fa4_migrated'][$new_icon] );
    $is_new = empty( $settings[$old_icon] ) && \Elementor\Icons_Manager::is_migration_allowed();

    $attributes['aria-hidden'] = 'true';
    $output = '';

    if ( woolentor_is_elementor_version( '>=', '2.6.0' ) && ( $is_new || $migrated ) ) {

        if ( empty( $settings[$new_icon]['library'] ) ) {
            return false;
        }

        $tag = 'i';
        // handler SVG Icon
        if ( 'svg' === $settings[$new_icon]['library'] ) {
            if ( ! isset( $settings[$new_icon]['value']['id'] ) ) {
                return '';
            }
            $output = Elementor\Core\Files\Assets\Svg\Svg_Handler::get_inline_svg( $settings[$new_icon]['value']['id'] );

        } else {
            $icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
            if ( isset( $icon_types[ $settings[$new_icon]['library'] ]['render_callback'] ) && is_callable( $icon_types[ $settings[$new_icon]['library'] ]['render_callback'] ) ) {
                return call_user_func_array( $icon_types[ $settings[$new_icon]['library'] ]['render_callback'], [ $settings[$new_icon], $attributes, $tag ] );
            }

            if ( empty( $attributes['class'] ) ) {
                $attributes['class'] = $settings[$new_icon]['value'];
            } else {
                if ( is_array( $attributes['class'] ) ) {
                    $attributes['class'][] = $settings[$new_icon]['value'];
                } else {
                    $attributes['class'] .= ' ' . $settings[$new_icon]['value'];
                }
            }
            $output = '<' . $tag . ' ' . \Elementor\Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
        }

    } else {
        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $settings[ $old_icon ];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $settings[ $old_icon ];
            } else {
                $attributes['class'] .= ' ' . $settings[ $old_icon ];
            }
        }
        $output = sprintf( '<i %s></i>', \Elementor\Utils::render_html_attributes( $attributes ) );
    }

    return $output;
 
}