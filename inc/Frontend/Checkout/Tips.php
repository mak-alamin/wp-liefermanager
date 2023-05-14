<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Checkout;

class Tips
{
    public function register()
    {
        // Check cart
        add_action('init', array($this, 'check_empty_cart'));

        // Generate tip fields
        add_action('woocommerce_after_order_notes', array($this, 'add_tip_option'));

        add_action('wp_enqueue_scripts', array($this, 'tip_scripts'));

        // Add Tip
        add_action('wp_ajax_wp_liefer_add_tip', array($this, 'wp_liefer_add_tip'));
        add_action('wp_ajax_nopriv_wp_liefer_add_tip', array($this, 'wp_liefer_add_tip'));

        // Remove Tip
        add_action('wp_ajax_wp_liefer_remove_tip', array($this, 'wp_liefer_remove_tip'));
        add_action('wp_ajax_nopriv_wp_liefer_remove_tip', array($this, 'wp_liefer_remove_tip'));

        // Add tip as a fee
        add_action('woocommerce_cart_calculate_fees', [$this, 'add_tip_as_custom_fee']);
    }

    public function check_empty_cart()
    {
        // Check if admin
        if (empty(WC()->cart)) {
            return;
        }

        // Check if cart empty
        if (WC()->cart->get_cart_contents_count() == 0) {
            WC()->session->set('tip_amount', 0);
        }
    }

    public function wp_liefer_add_tip()
    {
        $tipOption = (null !== $_REQUEST['tip_option']) ? floatval($_REQUEST['tip_option']) : '';

        $tipAmount = (null !== $_REQUEST['tip_amount']) ? floatval($_REQUEST['tip_amount']) : '';

        WC()->session->set('tip_option', $tipOption);
        WC()->session->set('tip_amount', $tipAmount);
    }

    public function wp_liefer_remove_tip()
    {
        WC()->session->set('tip_amount', 0);
    }

    function add_tip_as_custom_fee($cart)
    {
        $tip = WC()->session->get('tip_amount');

        if ($tip) {
            $cart->add_fee(_x('Trinkgeld', 'wp-liefermanager'), $tip, false);
        }
    }

    public function tip_scripts()
    {
        if (is_checkout()) {
            wp_enqueue_script('tip-scripts');
        }
    }

    public function add_tip_option()
    {
        $tip_type = carbon_get_theme_option('wp_liefer_tip_type');

        $tip_label = carbon_get_theme_option('wp_liefer_tip_label');

        $default_value = carbon_get_theme_option('wp_liefer_tip_default_value');

        if (WC()->session->get('tip_amount') == 0) {
            $tipValue = $default_value;
        } else {
            $tipValue = WC()->session->get('tip_amount');
        }

        if ($tip_type == 'percent') {
            $tipTypes = array(
                'percent'  => __('Prozent', 'wp-liefermanager'),
                // ''         => __('Kein Trinkgeld', 'wp-liefermanager'),
            );
        } else if ($tip_type == 'fixed') {
            $tipTypes = array(
                'fixed'    => __('Fester Betrag', 'wp-liefermanager'),
                // ''         => __('Kein Trinkgeld', 'wp-liefermanager'),
            );
        } else {
            $tipTypes = array(
                'fixed'    => __('Fester Betrag', 'wp-liefermanager'),
                'percent'  => __('Prozent', 'wp-liefermanager'),
                // ''         => __('Kein Trinkgeld', 'wp-liefermanager'),
            );
        }

        // woocommerce_form_field('tip_option', array(
        //     'type'        => 'select',
        //     'class'       => array('form-row-wide'),
        //     'label'       => __($tip_label, 'wp-liefermanager'),
        //     'options'     => $tipTypes,
        // ), WC()->session->get('tip_option'));

        woocommerce_form_field('tip_amount', array(
            'type'        => 'number',
            'class'       => array('form-row-wide'),
            'label'       => __($tip_label, 'wp-liefermanager') . ' (' . get_woocommerce_currency_symbol() . ')',
            'placeholder' => '',
            'custom_attributes' => array('min' => '0')
        ), $tipValue);

        echo '<button class="button calculate-tip">Trinkgeld geben</button>';
        echo '<button class="button remove-tip">Remove</button>';
    }
}
