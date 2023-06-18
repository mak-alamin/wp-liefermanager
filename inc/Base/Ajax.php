<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Base;

class Ajax
{
    public function register(){
        add_action('wp_ajax_wp_liefer_get_general_settings', array($this, 'get_general_settings'));

        add_action('wp_ajax_nopriv_wp_liefer_get_general_settings', array($this, 'get_general_settings'));
    }

    function get_general_settings(){
        $branchOption = carbon_get_theme_option('wp_liefer_branch_option');

        $delivery_type = carbon_get_theme_option('wp_liefer_delivery_type');

        $settings = [
            'branch_option' => $branchOption,
            'delivery_type' => $delivery_type,
        ];

        wp_send_json( $settings );
    }
}