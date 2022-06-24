;(function($){
"use strict";

    // Handle Quentity Increment and decrement
    var woolentor_checkout_quentity = {

        init: function(){
            $( document.body ).on( 'click', '.woolentor-order-review-product input.qty', this.update_order_review )
        },

        update_order_review: function( event ){

            var data = {
                action: 'update_order_review',
                security: wc_checkout_params.update_order_review_nonce,
                post_data: $( 'form.checkout' ).serialize()
            };
            $.post(
                wc_checkout_params.ajax_url,
                data, 
                function( response ){
                    $( document.body ).trigger( 'update_checkout' );
                }
            );

        }

    };

    // Handle Coupon Form
    var woolentor_checkout_coupons = {

        init: function() {
            $( document.body ).on( 'click', '.woolentor-checkout-coupon-form a.show-coupon', this.show_coupon_form )
            $( '.woolentor-checkout-coupon-form .coupon-form button[name="apply_coupon"]' ).on( 'click', this.submit );
        },

        show_coupon_form: function( event ) {
            event.preventDefault();

            $( '.woolentor-checkout-coupon-form .coupon-form' ).slideToggle( 400, function() {
                $( '.coupon-form' ).find( ':input:eq(0)' ).trigger( 'focus' );
            });

            return false;
        },

        submit: function( event ) {
            event.preventDefault();

            var $form = $('.woolentor-checkout-coupon-form').find(".coupon-form");

            if ( $form.is( '.processing' ) ) {
                return false;
            }

            $form.addClass( 'processing' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var data = {
                security:    wc_checkout_params.apply_coupon_nonce,
                coupon_code: $form.find( 'input[name="coupon_code"]' ).val()
            };

            $.ajax({
                type: 'POST',
                url:  wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'apply_coupon' ),
                data: data,
                success: function( code ) {
                    $( '.woocommerce-error, .woocommerce-message' ).remove();
                    $form.removeClass( 'processing' ).unblock();

                    if ( code ) {
                        $('.woolentor-checkout-coupon-form').after( code );
                        $form.slideUp();

                        $( document.body ).trigger( 'applied_coupon_in_checkout', [ data.coupon_code ] );
                        $( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );
                    }
                },
                dataType: 'html'
            });

            return false;
        }

    };

    // Handle Loggin Form
    var woolentor_checkout_login = {

        init: function() {
            $('.elementor-widget-wl-checkout-login-form').find('.woocommerce-form-login').removeAttr('method');
            $( document.body ).on( 'click', '.elementor-widget-wl-checkout-login-form a.showlogin', this.show_login_form );
            $( '.elementor-widget-wl-checkout-login-form button[name="login"]' ).on( 'click', this.submit_login );
        },

        show_login_form: function() {
            $( '.elementor-widget-wl-checkout-login-form div.login, .elementor-widget-wl-checkout-login-form div.woocommerce-form--login' ).slideToggle();
            return false;
        },

        submit_login: function( event ){
            event.preventDefault();

            var $form = $('.elementor-widget-wl-checkout-login-form').find(".woocommerce-form-login");

            if ( $form.is( '.processing' ) ) {
                return false;
            }

            $form.addClass( 'processing' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            // All Field value sent
            var item = {};
            var generateData = '';
            var loginformArea = $( '.elementor-widget-wl-checkout-login-form');
            loginformArea.find('input:text, input:password, input:file, input:hidden, select, textarea').each(function() {
                var $thisitem = $( this ),
                    attributeName = $thisitem.attr( 'name' ),
                    attributevalue = $thisitem.val();

                if ( attributevalue.length === 0 ) {
                    generateData = generateData + '&' + attributeName + '=' + '';
                } else {
                    item[attributeName] = attributevalue;
                    generateData = generateData + '&' + attributeName + '=' + attributevalue;
                }

            });
            loginformArea.find('input:radio, input:checkbox').each(function() {
                var $thisitem = $( this ),
                    attributeName = $thisitem.attr( 'name' ),
                    attributevalue = $thisitem.val();

                if( $thisitem.is(":checked") ){
                    generateData = generateData + '&' + attributeName + '=' + attributevalue;
                }

            });

            var generateData = '&login=login&action=woolentor_ajax_login' + generateData;

            $.ajax({  
                type: 'POST',
                url:  wc_checkout_params.ajax_url,
                data: JSON.stringify( generateData ),
                success: function( response ){
                    if( response.data ){
                        $('.elementor-widget-wl-checkout-login-form').find('.woocommerce-error').remove();
                        $form.removeClass( 'processing' ).unblock();
                        $('.elementor-widget-wl-checkout-login-form .woocommerce-form-login').before( response.data.notices );
                    }else{
                        window.location.replace( $( '.elementor-widget-wl-checkout-login-form').find('input[name="redirect"]').val() );
                    }
                }  
            });

        }

    };

    woolentor_checkout_quentity.init();
    woolentor_checkout_coupons.init();
    woolentor_checkout_login.init();


})(jQuery);