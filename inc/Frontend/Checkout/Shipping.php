<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Checkout;

use Inc\Base\Common;

class Shipping extends Common
{
    function register()
    {
        // Minimum Order Value
        add_action('woocommerce_check_cart_items', [$this, 'disable_checkout_for_min_cart_total']);

        // Set Delivery Option
        add_action('wp_ajax_wp_liefer_set_delivery_option', [$this, 'set_delivery_option']);
        add_action('wp_ajax_nopriv_wp_liefer_set_delivery_option', [$this, 'set_delivery_option']);
        
        // Delivery Cost
        add_action('woocommerce_cart_calculate_fees', [$this, 'add_delivery_cost_as_fee']);
    }

    // Minimum Order Value
    function disable_checkout_for_min_cart_total()
    {
        // Define the minimum cart total required for checkout
        $minimum_total = floatval(carbon_get_term_meta($this->get_branchId(), 'min_order_value'));

        // Get the cart total
        $cart_total = WC()->cart->subtotal;

        // Check if the cart total is less than the minimum requirement
        if ($cart_total < $minimum_total) {
            // Redirect the user to the cart page with an error message
            wc_add_notice('Mindestbestellwert ' . wc_price($minimum_total) . ' nicht erreicht.', 'error');
        }
    }

    // Delivery Cost
    function set_delivery_option()
    {
        $delivery_option = isset($_REQUEST['delivery_option']) ? $_REQUEST['delivery_option'] : 'delivery';

        WC()->session->set('delivery_option', $delivery_option);
    }

    function add_delivery_cost_as_fee()
    {
        $delivery_cost = floatval(carbon_get_term_meta($this->get_branchId(), 'delivery_cost'));

        $free_shipping_min_total = floatval(carbon_get_term_meta($this->get_branchId(), 'min_order_value_free_shipping'));

        // Check if the cart total is greater than a certain amount
        if ('delivery' == WC()->session->get('delivery_option') && WC()->cart->subtotal < $free_shipping_min_total) {
            // Add the delivery cost as a fee
            WC()->cart->add_fee('Lieferkosten', $delivery_cost, false);

            // Remove existing shipping methods
            WC()->session->set('chosen_shipping_methods', array());

            // Display a notice for free delivery
            wc_add_notice('Bei Bestellungen über ' . wc_price($free_shipping_min_total) . ' ist eine kostenlose Lieferung möglich.', 'notice');
        } else {
            // Remove the delivery cost fee
            if (!empty(WC()->cart->get_fees())) {
                foreach (WC()->cart->get_fees() as $fee_key => $fee) {
                    if ($fee->name === 'Lieferkosten') {
                        WC()->cart->remove_fee($fee_key);
                        break;
                    }
                }
            }
        }
    }
}
