<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Checkout;

class Delivery
{
    public function register()
    {
        // Add delivery/pickup date and time fields to checkout page
        add_action('woocommerce_checkout_before_customer_details', array($this, 'add_delivery_option'));

        // Validate delivery/pickup date and time fields
        add_action('woocommerce_checkout_process', array($this, 'validate_delivery_option'));

        // Save delivery/pickup date and time as order meta
        add_action('woocommerce_checkout_update_order_meta', array($this, 'save_delivery_options'));

        // Save delivery/pickup date and time to order
        add_action('woocommerce_checkout_create_order', array($this, 'save_delivery_pickup_info'));

        // Display delivery/pickup date and time in order details
        add_action('woocommerce_order_details_after_order_table', array($this, 'display_delivery_pickup_info'), 10, 1);

        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'display_delivery_pickup_info'), 10, 1);

        add_action('woocommerce_email_after_order_table', array($this, 'display_delivery_pickup_info'));

        // Enqueue delivery/pickup scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_delivery_scripts'));

        // Generate Delivery Times
        add_action('wp_ajax_wp_liefer_generate_delivery_times', array($this, 'wp_liefer_generate_delivery_times'));
        add_action('wp_ajax_nopriv_wp_liefer_generate_delivery_times', array($this, 'wp_liefer_generate_delivery_times'));

        // Generate Pickup Times
        add_action('wp_ajax_wp_liefer_generate_pickup_times', array($this, 'wp_liefer_generate_pickup_times'));
        add_action('wp_ajax_nopriv_wp_liefer_generate_pickup_times', array($this, 'wp_liefer_generate_pickup_times'));
    }

    public function add_delivery_option()
    {
        $tableId = intval(WC()->session->get('table_id'));

        if ($tableId) {
            return;
        }

        echo "<div class='wp-liefer-delivery-info'>";

        woocommerce_form_field('wp_liefer_delivery_option', array(
            'type'      => 'radio',
            'class'     => array('form-row-wide delivery-option'),
            'options'   => array(
                'delivery'  => __('Lieferung', 'wp-liefermanager'),
                'pickup'    => __('Abholung', 'wp-liefermanager')
            ),
            'default'   => 'delivery',
            'required'  => true,
        ), WC()->checkout->get_value('wp_liefer_delivery_option'));

        echo "<div class='delivery-datetime-picker'>";
        // woocommerce_form_field('wp_liefer_delivery_datepicker', array(
        //     'type'      => 'text',
        //     'label' => 'Lieferdatum',
        //     'placeholder' => 'Wählen Sie Lieferdatum',
        //     'class'     => array('form-row-wide'),
        //     'required'  => true,
        // ), WC()->checkout->get_value('chosen_delivery_date'));

        woocommerce_form_field('wp_liefer_delivery_timepicker', array(
            'type'      => 'text',
            'label' => 'Lieferzeit',
            'placeholder' => 'Wählen Sie Lieferzeit',
            'class'     => array('form-row-wide'),
            'required'  => true,
        ), WC()->checkout->get_value('chosen_delivery_time'));

        echo "</div>";

        echo "<div class='pickup-datetime-picker'>";
        // woocommerce_form_field('wp_liefer_pickup_datepicker', array(
        //     'type'      => 'text',
        //     'label' => 'Abholdatum',
        //     'placeholder' => 'Wählen Sie Abholdatum',
        //     'class'     => array('form-row-wide'),
        //     'required'  => true,
        // ), WC()->checkout->get_value('chosen_pickup_date'));

        woocommerce_form_field('wp_liefer_pickup_timepicker', array(
            'type'      => 'text',
            'label' => 'Abholzeit',
            'placeholder' => 'Wählen Sie Abholzeit',
            'class'     => array('form-row-wide'),
            'required'  => true,
        ), WC()->checkout->get_value('chosen_pickup_time'));

        echo "</div>";
        echo "</div>";
    }

    public function wp_liefer_generate_delivery_times()
    {
        $weekday = intval($_REQUEST['weekday']);

        $deliveryTimes = array();

        $deliveryTimes[1] = carbon_get_theme_option('wp_liefer_montag_delivery_times');

        $deliveryTimes[2] = carbon_get_theme_option('wp_liefer_dienstag_delivery_times');

        $deliveryTimes[3] = carbon_get_theme_option('wp_liefer_mittwoch_delivery_times');

        $deliveryTimes[4] = carbon_get_theme_option('wp_liefer_donnerstag_delivery_times');

        $deliveryTimes[5] = carbon_get_theme_option('wp_liefer_freitag_delivery_times');

        $deliveryTimes[6] = carbon_get_theme_option('wp_liefer_samstag_delivery_times');

        $deliveryTimes[0] = carbon_get_theme_option('wp_liefer_sontag_delivery_times');

        $openingTime = $deliveryTimes[$weekday][0]['open_at'];

        $lastHours = count($deliveryTimes[$weekday]) - 1;

        $closingTime = $deliveryTimes[$weekday][$lastHours]['close_at'];

        wp_send_json(array(
            'deliveryTimes' => $deliveryTimes[$weekday],
            'openingTime' => $openingTime,
            'closingTime' => $closingTime
        ));
    }

    public function wp_liefer_generate_pickup_times()
    {
        $weekday = intval($_REQUEST['weekday']);

        $pickupTimes = array();

        $pickupTimes[1] = carbon_get_theme_option('wp_liefer_montag_opening_hours');

        $pickupTimes[2] = carbon_get_theme_option('wp_liefer_dienstag_opening_hours');

        $pickupTimes[3] = carbon_get_theme_option('wp_liefer_mittwoch_opening_hours');

        $pickupTimes[4] = carbon_get_theme_option('wp_liefer_donnerstag_opening_hours');

        $pickupTimes[5] = carbon_get_theme_option('wp_liefer_freitag_opening_hours');

        $pickupTimes[6] = carbon_get_theme_option('wp_liefer_samstag_opening_hours');

        $pickupTimes[0] = carbon_get_theme_option('wp_liefer_sontag_opening_hours');

        $openingTime = $pickupTimes[$weekday][0]['open_at'];

        $lastHours = count($pickupTimes[$weekday]) - 1;

        $closingTime = $pickupTimes[$weekday][$lastHours]['close_at'];

        wp_send_json(array(
            'deliveryTimes' => $pickupTimes[$weekday],
            'openingTime' => $openingTime,
            'closingTime' => $closingTime
        ));
    }

    public function validate_delivery_option()
    {
        $tableId = intval(WC()->session->get('table_id'));

        if ($tableId) {
            return;
        }

        if (empty($_POST['wp_liefer_delivery_option'])) {
            wc_add_notice(__('Bitte wählen Sie die Liefer- oder Abholoption.', 'wp-liefermanager'), 'error');
        }

        if ($_POST['wp_liefer_delivery_option'] == 'delivery') {
            // if (empty($_POST['wp_liefer_delivery_datepicker'])) {
            //     wc_add_notice(__('Bitte wählen Sie ein Lieferdatum aus.', 'wp-liefermanager'), 'error');
            // }

            if (empty($_POST['wp_liefer_delivery_timepicker'])) {
                wc_add_notice(__('Bitte wählen Sie eine Lieferzeit aus.', 'wp-liefermanager'), 'error');
            }
        } else if ($_POST['wp_liefer_delivery_option'] == 'pickup') {
            // if (empty($_POST['wp_liefer_pickup_datepicker'])) {
            //     wc_add_notice(__('Bitte wählen Sie ein Abholdatum aus.', 'wp-liefermanager'), 'error');
            // }

            if (empty($_POST['wp_liefer_pickup_timepicker'])) {
                wc_add_notice(__('Bitte wählen Sie eine Abholzeit aus.', 'wp-liefermanager'), 'error');
            }
        }
    }

    public function save_delivery_options($order_id)
    {
        if ($_POST['wp_liefer_delivery_option']) {
            update_post_meta($order_id, 'wp_liefer_delivery_option', esc_attr($_POST['wp_liefer_delivery_option']));

            if ($_POST['wp_liefer_delivery_option'] == 'delivery') {
                if ($_POST['wp_liefer_delivery_datepicker']) {
                    update_post_meta($order_id, 'wp_liefer_delivery_date', esc_attr($_POST['wp_liefer_delivery_datepicker']));
                }

                if ($_POST['wp_liefer_delivery_timepicker']) {
                    update_post_meta($order_id, 'wp_liefer_delivery_time', esc_attr($_POST['wp_liefer_delivery_timepicker']));
                }
            } else if ($_POST['wp_liefer_delivery_option'] == 'pickup') {
                if ($_POST['wp_liefer_pickup_datepicker']) {
                    update_post_meta($order_id, 'wp_liefer_pickup_date', esc_attr($_POST['wp_liefer_pickup_datepicker']));
                }

                if ($_POST['wp_liefer_pickup_timepicker']) {
                    update_post_meta($order_id, 'wp_liefer_pickup_time', esc_attr($_POST['wp_liefer_pickup_timepicker']));
                }
            }
        }
    }

    function save_delivery_pickup_info($order)
    {
        $delivery_option = $order->get_meta('wp_liefer_delivery_option');

        if ($delivery_option == 'delivery') {
            $delivery_date = $order->get_meta('wp_liefer_delivery_date');
            $delivery_time = $order->get_meta('wp_liefer_delivery_time');

            $order->update_meta_data('Delivery Date', esc_html($delivery_date));
            $order->update_meta_data('Delivery Time', esc_html($delivery_time));
        } else if ($delivery_option == 'pickup') {
            $pickup_date = $order->get_meta('wp_liefer_pickup_date');
            $pickup_time = $order->get_meta('wp_liefer_pickup_time');

            $order->update_meta_data('Pickup Date', esc_html($pickup_date));
            $order->update_meta_data('Pickup Time', esc_html($pickup_time));
        }
    }

    public function display_delivery_pickup_info($order)
    {
        $order_id = is_object($order) ? $order->get_id() : $order;

        $tableId = get_post_meta($order_id, 'table_id', true);

        if ($tableId) {
            return;
        }

        $order = wc_get_order($order_id);

        $delivery_option = get_post_meta($order_id, 'wp_liefer_delivery_option', true);

        if ($delivery_option == 'delivery') {
            $delivery_date = get_post_meta($order_id, 'wp_liefer_delivery_date', true);
            $delivery_time = get_post_meta($order_id, 'wp_liefer_delivery_time', true);

            echo '<p><strong>' . __('Lieferdatum:', 'wp-liefermanager') . '</strong> ' . esc_html(date_format(date_create($delivery_date), 'j F, Y')) . '</p>';

            echo '<p><strong>' . __('Lieferzeit:', 'wp-liefermanager') . '</strong> ' . esc_html($delivery_time) . '</p>';
        } else if ($delivery_option == 'pickup') {
            $pickup_date = get_post_meta($order_id, 'wp_liefer_pickup_date', true);
            $pickup_time = get_post_meta($order_id, 'wp_liefer_pickup_time', true);

            echo '<p><strong>' . __('Abholdatum:', 'wp-liefermanager') . '</strong> ' . esc_html(date_format(date_create($pickup_date), 'j F, Y')) . '</p>';

            echo '<p><strong>' . __('Abholzeit:', 'wp-liefermanager') . '</strong> ' . esc_html($pickup_time) . '</p>';
        }
    }


    public function enqueue_delivery_scripts()
    {
        wp_enqueue_style('jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style('jquery-timepicker');

        wp_enqueue_script('jquery-block');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-timepicker');
        wp_enqueue_script('delivery-scripts');
    }
}
