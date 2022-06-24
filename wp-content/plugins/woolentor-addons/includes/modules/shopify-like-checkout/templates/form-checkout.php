<?php
/**
 * Checkout Form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$blog_info       = get_bloginfo( 'name' );
$get_custom_logo = woolentor_get_option( 'logo', 'woolentor_shopify_checkout_settings', '' );
$get_custom_menu_id = woolentor_get_option( 'custommenu', 'woolentor_shopify_checkout_settings', '0' );
$menu_html = '';

if( !empty( $get_custom_menu_id ) ){
    $custom_menuargs = [
        'echo'       => false,
        'menu'       => $get_custom_menu_id,
        'menu_class' => 'woolentor-checkout__policy-list',
        'menu_id'    => 'menu-'. $get_custom_menu_id,
        'add_li_class'=> 'woolentor-checkout__policy-item',
        'fallback_cb' => '__return_empty_string',
        'container'   => '',
    ];
    // General Menu.
    $menu_html = wp_nav_menu( $custom_menuargs );
}

?>

<div class="woolentor-checkout__box woolentor-step--info">
    <div class="woolentor-checkout__container">

        <div class="woolentor-checkout__left-sidebar">
            <div class="woolentor-checkout__header">
                <div class="woolentor-checkout__logo">
                    <?php 
                        if( !empty( $get_custom_logo ) ){
                            echo sprintf('<img src="%s" alt="%s" />',esc_url( $get_custom_logo ), $blog_info );
                        }else if( has_custom_logo() ){
                            ?><div class="site-logo"><?php the_custom_logo(); ?></div><?php
                        }else{
                            echo sprintf('<h1 class="site-title"><a href="%s">%s</a></h1>', esc_url( home_url( '/' ) ), esc_html( $blog_info ) );
                        }
                    ?>
                </div>
                <ul class="woolentor-checkout__breadcrumb">
                    <li class="woolentor-checkout__breadcrumb-item">
                        <a class="woolentor-checkout__breadcrumb-link" href="<?php echo esc_url(wc_get_cart_url()) ?>"><?php echo esc_html__('Cart','woolentor') ?></a>
                    </li>
                    <li class="woolentor-checkout__breadcrumb-item active" data-step="step--info">
                        <span class="woolentor-checkout__breadcrumb-text"><?php echo esc_html__('Information', 'woolentor') ?></span>
                    </li>
                    </li>
                    <li class="woolentor-checkout__breadcrumb-item" data-step="step--shipping">
                        <span class="woolentor-checkout__breadcrumb-text"><?php echo esc_html__('Shipping', 'woolentor') ?></span>
                    </li>
                    <li class="woolentor-checkout__breadcrumb-item" data-step="step--payment">
                        <span class="woolentor-checkout__breadcrumb-text"><?php echo esc_html__('Payment', 'woolentor') ?></span>
                    </li>
                </ul>
            </div>
            <div class="woolentor-checkout__body">
                <?php if( !is_user_logged_in() ): ?>
                    <div class="woolentor-checkout__section woolentor-contact-info">
                        <div class="woolentor-checkout__section-header">
                            <h2 class="woolentor-checkout__section-title">
                                <?php echo esc_html__('Contact information', 'woolentor') ?>
                            </h2>

                            
                            <p class="woolentor-checkout__item-col">
                                <?php echo esc_html__('Already have an account?', 'woolentor') ?>
                                <a  class="showlogin" href="#"><?php echo esc_html__('Log in', 'woolentor') ?></a>
                            </p>
                        </div>

                        <?php
                        woocommerce_login_form(
                            array(
                                'message'  => esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woolentor' ),
                                'redirect' => wc_get_checkout_url(),
                                'hidden'   => true,
                            )
                        );
                        ?>
                    </div>
                    <?php endif; ?>

                    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                        <?php if( is_user_logged_in() ): ?>
                        <div class="woolentor-checkout__section woolentor-contact-info">
                            <div class="woolentor-checkout__section-header">
                                <h2 class="woolentor-checkout__section-title">
                                    <?php echo esc_html__('Contact information', 'woolentor') ?>
                                </h2>
                            </div>
                        </div>
                        <?php endif; ?>

                    <!-- Shipping address Start -->
                    <div class="woolentor-checkout__section woolentor-step--info">
                        <?php $checkout->checkout_form_billing(); ?>

                        <?php
                            if ( true === WC()->cart->needs_shipping_address() ){
                                $checkout->checkout_form_shipping();
                            }
                        ?>
                        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
                    </div>
                    <!-- Shipping address End -->

                    <div class="woolentor-checkout__section woolentor-step--shipping">
                        <div class="woolentor-checkout__section-header">
                            <h2 class="woolentor-checkout__section-title">
                                <?php echo esc_html__('Shipping Method', 'woolentor') ?>
                            </h2>
                        </div>
                        <table class="woolentor-checkout__shipping-method">
                            <tbody>
                                <?php wc_cart_totals_shipping_html(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment -->
                    <div class="woolentor-checkout__section woolentor-step--payment">
                        <div class="woolentor-checkout__section-header">
                            <h2 class="woolentor-checkout__section-title">
                                <?php echo esc_html__('Choose a Payment Gateway', 'woolentor') ?>
                            </h2>
                        </div>
                        <div class="woolentor-checkout__row">
                            <?php woocommerce_checkout_payment(); ?>
                        </div>
                    </div>
                    <!-- Payment -->

                    <?php
                        $terms_page_id   = wc_terms_and_conditions_page_id();
                        $terms_page      = $terms_page_id ? get_post( $terms_page_id ) : false;
                        $terms_page_link = get_permalink($terms_page_id);

                        $policy_page_id     = (int) get_option( 'wp_page_for_privacy_policy' );
                        $has_footer_menu = '';
                        if( $terms_page_id || $policy_page_id ){
                            $has_footer_menu = true;
                        }
                    ?>
                    <div class="woolentor-checkout__section woolentor-has-footer-menu--<?php echo esc_attr($has_footer_menu ? 'yes' : ''); ?>">
                        <div class="woolentor-checkout__step-footer woolentor-footer--1">
                            <a href="#" data-step="step--shipping" class="woolentor-checkout__button" type="submit"><?php echo esc_html__('Continue to shipping', 'woolentor') ?></a>
                            <a href="<?php echo esc_url(wc_get_cart_url()) ?>" class="woolentor-checkout__text-link"><?php echo esc_html__('Return to cart', 'woolentor') ?></a>
                        </div>
                        <div class="woolentor-checkout__step-footer step--shipping woolentor-footer--2">
                            <a href="#" data-step="step--payment" class="woolentor-checkout__button" type="submit"><?php echo esc_html__('Continue to Payment', 'woolentor') ?></a>
                            <a href="#" data-step="step--info" class="woolentor-checkout__text-link"><?php echo esc_html__('Return to informations', 'woolentor') ?></a>
                        </div>
                        <div class="woolentor-checkout__step-footer step--payment woolentor-footer--3">
                            <div>
                                <?php wc_get_template( 'checkout/terms.php' ); ?>
                            </div>
                            <div>
                                <?php
                                    $order_button_text = apply_filters( 'woocommerce_order_button_text', __('Place order', 'woolentor') );
                                    echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="woolentor-checkout__button" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

                                <a href="#" data-step="step--shipping" class="woolentor-checkout__text-link"><?php echo esc_html__('Return to shipping', 'woolentor') ?></a>
                            </div>
                        </div>
                    </div>

                    <?php
                        if( $has_footer_menu ):
                    ?>
                    <div class="woolentor-checkout__footer">
                        <?php
                            if( !empty( $menu_html ) ):
                                echo $menu_html;
                            else:
                        ?>
                            <ul class="woolentor-checkout__policy-list">
                                <?php if($policy_page_id): ?>
                                    <li><a href="<?php echo esc_url(get_permalink($policy_page_id)) ?>"><?php echo esc_html(get_the_title($policy_page_id)); ?></a></li>
                                <?php endif; ?>

                                <?php if($terms_page_link): ?>
                                    <li><a href="<?php echo esc_url($terms_page_link) ?>"><?php echo esc_html(get_the_title($terms_page_id)) ?></a></li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php
                        endif;
                    ?>
                </form>

            </div>
        </div>

        <div class="woolentor-checkout__right-sidebar woolentor-shipping-status--<?php echo esc_attr(WC()->cart->needs_shipping_address() ? 'yes' : 'no') ?>">
            <div class="woolentor-checkout__prduct-box">
                <?php
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        ?>
                        <!-- Single product Start -->
                        <div class="woolentor-checkout__product">
                            <div class="woolentor-checkout__product-left">
                                <div class="woolentor-checkout__product-image">
                                    <div class="woolentor-checkout__product-thumbnail">
                                        <?php
                                            if($_product->get_image_id()){
                                                echo wp_kses_post(wp_get_attachment_image($_product->get_image_id()));
                                            } else {
                                                echo wc_placeholder_img();
                                            }
                                        ?>
                                    </div>
                                    <span class="woolentor-checkout__product-quantity"><?php echo esc_html($cart_item['quantity']) ?></span>
                                </div>
                                <div class="woolentor-checkout__product-description">
                                    <span class="woolentor-checkout__product-name"><a href="<?php echo esc_url( get_permalink($_product->get_id()) ); ?>"><?php echo esc_html($_product->get_name()); ?></a></span>
                                </div>
                            </div>
                            <div class="woolentor-checkout__product-price-box">
                                <span class="woolentor-checkout__product-price"><?php echo wp_kses_post(wc_price($_product->get_price())) ?></span>
                            </div>
                        </div>
                        <!-- Single product End -->
                        <?php
                    }
                }
                ?>

            </div>

            <!-- Coupon Code -->
            <?php woocommerce_checkout_coupon_form(); ?>

            <!-- Review order -->
            <?php woocommerce_order_review(); ?>

        </div>
    </div>
</div>