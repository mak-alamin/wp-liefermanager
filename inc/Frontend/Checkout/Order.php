<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Checkout;

use Inc\Base\Common;

class Order extends Common
{
    function register() {
        // add_action('woocommerce_checkout_before_customer_details', array($this, 'test_function'));

        add_filter( 'woocommerce_email_headers', [$this, 'add_branch_email_for_order'], 10, 2);
    }

 
    public function add_branch_email_for_order( $headers, $object ) {
        $selectedBranch = isset($_COOKIE['wp_liefer_selected_branch']) ? $_COOKIE['wp_liefer_selected_branch'] : null;
        
        $branchInfo = $this->get_branchInfo($selectedBranch);

        $branchEmail = isset($branchInfo->id) ? carbon_get_term_meta($branchInfo->id, 'branch_email') : '';

        $headers .= 'CC: Neue Bestellung <'. $branchEmail .'>' . "\r\n";

        return $headers;
    }
}