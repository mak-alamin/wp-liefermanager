<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Checkout;

use Inc\Base\Common;

class Delivery extends Common
{
    protected $weekdays = array(
        1 => 'montag',
        2 => 'dienstag',
        3 => 'mittwoch',
        4 => 'donnerstag',
        5 => 'freitag',
        6 => 'samstag',
        0 => 'sontag'
    );

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

        $selectedBranch = isset($_COOKIE['wp_liefer_selected_branch']) ? $_COOKIE['wp_liefer_selected_branch'] : null;

        $branchOption = carbon_get_theme_option('wp_liefer_branch_option');

        if ('multi' == $branchOption && $this->get_branch_name($selectedBranch)) {
            echo "<h3>Filiale: " . $this->get_branch_name($selectedBranch) . "</h3>";
        }

        $delivery_type = carbon_get_theme_option('wp_liefer_delivery_type');

        if ($delivery_type == 'disable') {
            return;
        }

        $options = array(
            'delivery'  => __('Lieferung', 'wp-liefermanager'),
            'pickup'    => __('Abholung', 'wp-liefermanager')
        );

        if($delivery_type == 'delivery_only'){
            $options = array(
                'delivery'  => __('Lieferung', 'wp-liefermanager'),
            );
        }
        
        if($delivery_type == 'pickup_only'){
            $options = array(
                'pickup'  => __('Abholung', 'wp-liefermanager'),
            );
        }

        echo "<div class='wp-liefer-delivery-info'>";

        woocommerce_form_field('wp_liefer_delivery_option', array(
            'type'      => 'radio',
            'class'     => array('form-row-wide delivery-option'),
            'options'   => $options,
            'default'   => 'delivery',
            'required'  => true,
        ), WC()->session->get('wp_liefer_delivery_option'));

        if ($delivery_type == 'delivery_pickup' || $delivery_type == 'delivery_only') {
            echo "<div class='delivery-datetime-picker'>";
            // woocommerce_form_field('wp_liefer_delivery_datepicker', array(
            //     'type'      => 'text',
            //     'label' => 'Lieferdatum',
            //     'placeholder' => 'Wählen Sie Lieferdatum',
            //     'class'     => array('form-row-wide'),
            //     'required'  => true,
            // ), WC()->session->get('chosen_delivery_date'));

            woocommerce_form_field('wp_liefer_delivery_timepicker', array(
                'type'      => 'text',
                'label' => 'Lieferzeit',
                'placeholder' => 'Wählen Sie Lieferzeit',
                'class'     => array('form-row-wide'),
                'required'  => true,
            ), WC()->session->get('chosen_delivery_time'));

            echo "</div>";
        }

        if ($delivery_type == 'delivery_pickup' || $delivery_type == 'pickup_only') {
            echo "<div class='pickup-datetime-picker'>";
            // woocommerce_form_field('wp_liefer_pickup_datepicker', array(
            //     'type'      => 'text',
            //     'label' => 'Abholdatum',
            //     'placeholder' => 'Wählen Sie Abholdatum',
            //     'class'     => array('form-row-wide'),
            //     'required'  => true,
            // ), WC()->session->get('chosen_pickup_date'));

            woocommerce_form_field('wp_liefer_pickup_timepicker', array(
                'type'      => 'text',
                'label' => 'Abholzeit',
                'placeholder' => 'Wählen Sie Abholzeit',
                'class'     => array('form-row-wide'),
                'required'  => true,
            ), WC()->session->get('chosen_pickup_time'));

            echo "</div>";
        }

        echo "</div>";
    }

    public function wp_liefer_generate_delivery_times()
    {
        $weekday = intval($_REQUEST['weekday']);
        $branchId = intval($_REQUEST['branchId']);

        $deliveryTimes = array();

        foreach ($this->weekdays as $key => $dayname) {
            if ($branchId) {
                $deliveryTimes[$key] =  carbon_get_term_meta($branchId, 'wp_liefer_' . $dayname . '_delivery_times');
            } else {
                $deliveryTimes[$key] = carbon_get_theme_option('wp_liefer_' . $dayname . '_delivery_times');
            }
        }

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

        $branchId = intval($_REQUEST['branchId']);

        $pickupTimes = array();

        foreach ($this->weekdays as $key => $dayname) {
            if ($branchId) {
                $pickupTimes[$key] =  carbon_get_term_meta($branchId, 'wp_liefer_' . $dayname . '_opening_hours');
            } else {
                $pickupTimes[$key] = carbon_get_theme_option('wp_liefer_' . $dayname . '_opening_hours');
            }
        }

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

        $delivery_type = carbon_get_theme_option('wp_liefer_delivery_type');

        if ($delivery_type == 'disable') {
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
        $selectedBranch = isset($_COOKIE['wp_liefer_selected_branch']) ? $_COOKIE['wp_liefer_selected_branch'] : null;

        if (!empty($selectedBranch)) {
            update_post_meta($order_id, 'wp_liefer_selected_branch', $selectedBranch);
        }

        $delivery_type = carbon_get_theme_option('wp_liefer_delivery_type');

        if ($delivery_type == 'disable') {
            return;
        }

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
        $selectedBranch = isset($_COOKIE['wp_liefer_selected_branch']) ? $_COOKIE['wp_liefer_selected_branch'] : null;

        $branchOption = carbon_get_theme_option('wp_liefer_branch_option');

        if ('multi' == $branchOption && $this->get_branch_name($selectedBranch)) {
            $order->update_meta_data('Filiale', esc_html($this->get_branch_name($selectedBranch)));
        }

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

        $selectedBranch = get_post_meta($order_id, 'Filiale', true);

        if ($selectedBranch) {
            echo '<p><strong>' . __('Filiale:', 'wp-liefermanager') . '</strong> ' . esc_html($selectedBranch) . '</p>';
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
    }
}
