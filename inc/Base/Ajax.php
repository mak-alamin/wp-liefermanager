<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Base;

class Ajax
{
    public function register(){
        // General Settings
        add_action('wp_ajax_wp_liefer_get_general_settings', array($this, 'get_general_settings'));

        add_action('wp_ajax_nopriv_wp_liefer_get_general_settings', array($this, 'get_general_settings'));
     
        // Zipcodes
        add_action('wp_ajax_wp_liefer_get_zipcodes', array($this, 'get_zipcodes'));

        add_action('wp_ajax_nopriv_wp_liefer_get_zipcodes', array($this, 'get_zipcodes'));
    }

    function get_zipcodes() {
        $branchOption = carbon_get_theme_option('wp_liefer_branch_option');
       
        $branchId = intval($_REQUEST['branch_id']);
        
        $zipcodes = array();
        
        if($branchOption == 'multi'){
            $zipcodes = carbon_get_term_meta($branchId, 'zipcodes');
        } else {
            $zipcodes = carbon_get_theme_option('wp_liefer_zipcodes');
        }

        $zipcodes = array_map('trim', explode(',', $zipcodes) );

        wp_send_json($zipcodes);
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