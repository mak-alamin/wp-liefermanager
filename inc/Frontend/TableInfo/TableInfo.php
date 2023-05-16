<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\TableInfo;

class TableInfo
{
    public function register()
    {
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_table_info_to_cart_item'], 10, 2);

        // add_filter('woocommerce_get_item_data', [$this, 'display_table_info_on_cart'], 10, 2);

        // Add Table Info to checkout page
        add_action('woocommerce_checkout_before_customer_details', array($this, 'show_table_info'));

        // Save Table Info to order
        add_action('woocommerce_checkout_create_order', array($this, 'save_table_info'));

        // Display Table Info in order details
        add_action('woocommerce_order_details_after_order_table', array($this, 'display_table_info_in_order'), 10, 1);

        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'display_table_info_in_order'), 10, 1);
    }

    // Add table info to cart
    public function add_table_info_to_cart_item($cart_item_data, $product_id)
    {
        $tableId = isset($_REQUEST['table_id']) ? $_REQUEST['table_id'] : 0;

        $cart_item_data['table_id'] = $tableId;
    }

    // Show table info to checkout
    public function show_table_info()
    {
        $tableId = intval(WC()->session->get('table_id'));

        if ($tableId) {
            $this->generateTableName($tableId);
        }
    }

    private function generateTableName($tableId)
    {
        $args = array(
            'post_type' => "wp-liefer-tables",
            'meta_query' => array(
                array(
                    'key' => 'wp_liefer_table_id',
                    'value' => $tableId,
                )
            )
        );

        $tables = get_posts($args);

        if (!empty($tables)) {
            echo "<h3>Tischnummer: " . $tables[0]->post_title . "</h3>";
        }
    }

    // Save Table Info
    public function save_table_info($order)
    {
        $tableId = intval(WC()->session->get('table_id'));

        if ($tableId) {
            $order->update_meta_data('table_id', esc_html($tableId));
        }

        WC()->session->set('table_id', 0);
    }

    // Display table info in order
    public function display_table_info_in_order($order)
    {
        $order_id = is_object($order) ? $order->get_id() : $order;

        $tableId = get_post_meta($order_id, 'table_id', true);

        if ($tableId) {
            $this->generateTableName($tableId);
        }
    }
}
