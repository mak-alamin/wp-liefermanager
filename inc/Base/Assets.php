<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Base;

class Assets
{
    public function register()
    {
        add_action('wp_enqueue_scripts', [$this, 'register_frontend_scripts']);

        add_action('admin_enqueue_scripts', [$this, 'register_admin_scripts']);
    }

    public function register_frontend_scripts()
    {
        $css_files = array_diff(scandir(WP_LIEFERMANAGER_PLUGIN_DIR . 'assets/frontend/css'), array('.', '..'));

        $js_files = array_diff(scandir(WP_LIEFERMANAGER_PLUGIN_DIR . 'assets/frontend/js'), array('.', '..'));

        if (!empty($css_files)) {
            foreach ($css_files as $key => $file) {
                wp_register_style(basename($file, '.css'), WP_LIEFERMANAGER_ASSETS . '/frontend/css/' . $file, array(), time(), 'all');
            }
        }

        if (!empty($js_files)) {
            foreach ($js_files as $key => $file) {
                $js_handle = basename($file, '.js');

                $js_footer = true;

                if($js_handle == 'wp-liefer-common'){
                    $js_footer = false;
                }

                wp_register_script($js_handle, WP_LIEFERMANAGER_ASSETS . '/frontend/js/' . $file, array('jquery'), time(), $js_footer);
            }
        }

        wp_enqueue_style('frontend-main');

        wp_enqueue_script('wp-liefer-common');
        wp_enqueue_script('wp-liefer-main');
        wp_enqueue_script('delivery-scripts');

        $branches = get_terms(array(
            'taxonomy' => 'wp_liefer_branches',
            'post_type' => 'product'
        ));

        $branchOption = carbon_get_theme_option('wp_liefer_branch_option');

        $delivery_type = carbon_get_theme_option('wp_liefer_delivery_type');

        $settings = [
            'branch_option' => $branchOption,
            'delivery_type' => $delivery_type,
        ];

        wp_localize_script('wp-liefer-common', 'WPLiefermanagerData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'branches' => $branches,
            'settings' => $settings
        ));
    }

    public function register_admin_scripts($hook)
    {
        $css_files = array_diff(scandir(WP_LIEFERMANAGER_PLUGIN_DIR . 'assets/admin/css'), array('.', '..'));

        $js_files = array_diff(scandir(WP_LIEFERMANAGER_PLUGIN_DIR . 'assets/admin/js'), array('.', '..'));

        if (!empty($css_files)) {
            foreach ($css_files as $key => $file) {
                wp_register_style(basename($file, '.css'), WP_LIEFERMANAGER_ASSETS . '/admin/css/' . $file, array(), time(), 'all');
            }
        }

        if (!empty($js_files)) {
            foreach ($js_files as $key => $file) {
                wp_register_script(basename($file, '.js'), WP_LIEFERMANAGER_ASSETS . '/admin/js/' . $file, array('jquery'), time(), true);
            }
        }

        wp_enqueue_style('wp-liefer-admin-main');
        wp_enqueue_script('wp-liefer-admin-main');
    }
}
