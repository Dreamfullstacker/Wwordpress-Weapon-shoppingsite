<?php  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woolentor_Flash_Sale{

    private static $_instance = null;

    /**
     * Get Instance
     */
    public static function get_instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public $options = array();

    public $the_product = '';

    /**
     * Constructor
     */
    public function __construct(){

        // Enqueue scripts
        add_action('wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        // Change display price for loop product and product details
        add_filter( 'woocommerce_get_price_html', [ $this, 'alter_display_price' ], 10, 2 );

        // Change price in cart
        add_action( 'woocommerce_before_calculate_totals', [ $this, 'alter_price_in_cart' ], 9999 );

        // Countdown position
        $position = woolentor_get_option( 'countdown_position', 'woolentor_flash_sale_settings', 'woocommerce_before_add_to_cart_form' );
        add_action( $position, [ $this, 'render_countdown' ] );

    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts(){

        if( is_product() ){

            wp_enqueue_script( 'moment', plugin_dir_url( __FILE__ ) . '/assets/js/moment.min.js', array('jquery'), '2.29.1' );
            wp_enqueue_script( 'moment-timezone-with-data', plugin_dir_url( __FILE__ ) . '/assets/js/moment-timezone-with-data.js', array('jquery','moment'), '0.5.34' );

            wp_enqueue_script( 'woolentor-flash-sale-module', plugin_dir_url( __FILE__ ) . '/assets/js/flash-sale.js', array('countdown-min'), WOOLENTOR_VERSION );
            wp_enqueue_style( 'woolentor-flash-sale-module', plugin_dir_url( __FILE__ ) . '/assets/css/flash-sale.css', '', WOOLENTOR_VERSION, 'all' );

            wp_localize_script( 'woolentor-flash-sale-module', 'woolentor_flash_sale_module', array(
                'timeZone' => wp_timezone_string()
            ) );

        }
        
    }

    /**
     * Validate user previllage
     */
    public static function user_validity( $deal ){
        $validity                            = false;
        $apply_only_for_registered_customers = !empty($deal['apply_discount_only_for_registered_customers']) ? $deal['apply_discount_only_for_registered_customers'] : '';
        $allowed_user_roles                  = !empty($deal['allowed_user_roles']) ? explode(',',  $deal['allowed_user_roles']) : array();

        if( $apply_only_for_registered_customers ){
            if( is_user_logged_in() && !$allowed_user_roles ){
                $validity = true;
            }

            if( is_user_logged_in() && $allowed_user_roles ){
                $current_user_obj   = get_user_by( 'id', get_current_user_id() );
                $current_user_roles = $current_user_obj->roles;

                if( array_intersect( $current_user_roles, $allowed_user_roles) ){
                    $validity = true;
                }
            }
        } else{
            $validity = true;
        }

        return $validity;
    }

    /**
     * Validate offer date & time
     */
    public static function datetime_validity( $deal ){
        $validity        = false;
        $current_time    = current_time('timestamp');
        $deal_time_began = strtotime( $deal['start_date'] );
        $deal_time_end   = strtotime( $deal["end_date"] );
        $deal_time_end   = $deal_time_end + 86399; // 23 hours + 59 minutes as the end date

        // if any one of the time is defined
        if( $deal_time_began || $deal_time_end ){

            // if began datetime is set but end time not set
            if( $deal_time_began && empty($deal_time_end) ){
                if( $deal_time_began <= $current_time ){
                    $validity = true;
                }
            }
            // if end datetime is set but start datetime not set
            elseif( $deal_time_end && empty($deal_time_began) ) {
                if( $current_time <= $deal_time_end ){
                    $validity = true;
                }
            // if both time is set
            } else {
                if( ($current_time >= $deal_time_began) && ($current_time <= $deal_time_end) ){
                    $validity = true;
                }
            }

        } else {
            $validity = true;
        }

        return $validity;
    }

    /**
     * Check & validate if a product has any deal or not
     */
    public static function products_validity( $product, $deal ){
        $validity              = false;

        $apply_on_all_products = !empty( $deal['apply_on_all_products'] ) ? $deal['apply_on_all_products'] : '';
        $applicable_categories = !empty( $deal['categories'] ) ? $deal['categories'] : array();
        $applicable_products   = !empty( $deal['products'] ) ? $deal['products'] : array();
        $exclude_products      = !empty( $deal['exclude_products'] ) ? $deal['exclude_products'] : array();

        if( ! $product ){
            return false;
        }

        // Exlcude products
        if( in_array( $product->get_id(), $exclude_products ) ){
            return false;
        }

        if( $apply_on_all_products ){
            return true;
        } elseif( $applicable_categories || $applicable_products ) {
            $current_product_categories = wc_get_product_term_ids( $product->get_id(), 'product_cat' );
            if( array_intersect( $applicable_categories, $current_product_categories ) ){
                return true;
            } elseif( in_array($product->get_id(), $applicable_products) ){
                return true;
            }
        }

        return $validity;
    }

    /**
     * Loop through each deals and get the first matched deal for the given product
     */
    public static function get_deal( $product ){
        $flash_sale_settings = get_option('woolentor_flash_sale_settings');

        if( isset( $flash_sale_settings['deals'] ) && is_array( $flash_sale_settings['deals'] ) ){
            foreach( $flash_sale_settings['deals'] as $key => $deal ){
                $status = !empty($deal['status']) ? $deal['status'] : 'off';
                if( $status != 'on' ){
                    continue;
                }

                if( self::user_validity($deal) && self::datetime_validity($deal) && self::products_validity($product, $deal) ){
                    return $deal;
                    break;
                }

            }
        }

        return array();
    }

    /**
     * One a deal found for the given product
     * Calculate the the price based on the discount/deal matched with the given product
     */
    public function get_calculated_price( $product, $deal ){
        $orig_price     = (float) $product->get_regular_price();

        $discount_type  = !empty($deal['discount_type']) ? $deal['discount_type'] : 'fixed_discount';
        $discount_value = !empty($deal['discount_value']) ? $deal['discount_value'] : '';

        $override_sale_price = woolentor_get_option('override_sale_price', 'woolentor_flash_sale_settings');
        if( $override_sale_price && $product->is_on_sale() ){
            $orig_price = wc_get_price_to_display( $product );
        }

        // prepare discounted price
        if( $discount_type == 'fixed_discount' ){

            return $orig_price -  (float) $discount_value;

        } elseif( $discount_type == 'percentage_discount' ){

            return $orig_price * (1 -  (float) $discount_value / 100);

        } elseif( $discount_type == 'fixed_price' ){

            return  (float) $discount_value;

        }
    }

    /**
     * Alter the display price of the given product by the new discounted price
     * Altering product price does not apply when a customer add to cart the product
     * This process just for showing discounted price to the customers
     */
    public function alter_display_price( $price_html, $product ){
        // Only on frontend includeing elementor editor
        if( is_admin() && !\Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return $price_html;
        }

        // Only if price is not null
        if( '' === $product->get_price() ){
            return $price_html;
        }

        if( $product->get_type() != 'variation' ){
            $this->the_product = $product;
        }

        $deal           = self::get_deal( $this->the_product );
        $discount_value = !empty($deal['discount_value']) ? $deal['discount_value'] : '';

        if( $deal && self::datetime_validity($deal) && $discount_value ){

            if( $product->get_type() == 'simple' || $product->get_type() == 'external' ){

                $calculated_price  = $this->get_calculated_price( $product, $deal );
                $price_html = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), $calculated_price ) . $product->get_price_suffix();
            }

        }

        return $price_html;
    }
    
    /**
     * Loop throguh each products in cart
     * get the discounted price by the given product and 
     * apply the new price customers should pay
     */
    public function alter_price_in_cart( $cart ){
        if( is_admin() && ! defined( 'DOING_AJAX' ) ){
            return;
        }
     
        if( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ){
            return;
        }
     
        // Loop through cart items & apply discount
        foreach( $cart->get_cart() as $cart_item_key => $cart_item ){
            $product        = $cart_item['data'];
            $price          = $product->get_price();
            $deal           = $this->get_deal( $product );
            $discount_value = !empty($deal['discount_value']) ? $deal['discount_value'] : '';

            if( $deal && $discount_value ){

                if($product->get_type() == 'simple' || $product->get_type() == 'external' ){
                    $price = $this->get_calculated_price( $product, $deal );
                }

            }

            $cart_item['data']->set_price( $price );
        }
    }

    /**
     * Render countdown
     */
    public function render_countdown(){
        if( !is_product() ){
            return;
        }

        global $product;
        $flash_sale_settings = get_option('woolentor_flash_sale_settings');
        $countdown_heading   = woolentor_get_option('countdown_timer_title', 'woolentor_flash_sale_settings', esc_html__('Hurry Up! Offer ends in', 'woolentor'));
        $deal                = self::get_deal($product);
        $countdown_status    = woolentor_get_option('enable_countdown_on_product_details_page', 'woolentor_flash_sale_settings', 'on');
        $deal_time_end       = !empty($deal['end_date']) ? date('Y-m-d', strtotime($deal['end_date']) + 86400) : ''; // formate datetime for moment timezone like this 2021-12-19 19:20, otherwise the moment timezone produce a deperecated notice

        // Right now support only simple product
        if( $product->get_type() != 'simple' ){
            return;
        }

        if( $deal && $deal_time_end && $countdown_status == 'on' ):

            $custom_labels = apply_filters('woolentor_countdown_custom_labels', array(
                'daytxt'     => esc_html__('Days', 'woolentor'),
                'hourtxt'    => esc_html__('Hours', 'woolentor'),
                'minutestxt' => esc_html__('Min', 'woolentor'),
                'secondstxt' => esc_html__('Sec', 'woolentor')
            ));
        ?>
        <div class="ht-saleflash-countdown-wrap ht-align--left">
            <span class="ht-saleflash-countdown-title"><?php echo wp_kses_post( $countdown_heading ) ?></span>
            <div class="ht-product-countdown" data-countdown="<?php echo esc_attr( $deal_time_end ) ?>" data-customlavel='<?php echo json_encode( $custom_labels ) ?>'></div>
        </div>
        <?php
        endif;
    }
}

Woolentor_Flash_Sale::get_instance();