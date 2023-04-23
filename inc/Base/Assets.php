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
                wp_register_script(basename($file, '.js'), WP_LIEFERMANAGER_ASSETS . '/frontend/js/' . $file, array('jquery'), time(), true);
            }
        }

        wp_enqueue_style('frontend-main');

        wp_enqueue_script('main');

        wp_localize_script('main', 'WPLiefermanagerData', array(
            'ajaxurl' => admin_url('admin-ajax.php')
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

        wp_enqueue_style('admin-main');
        wp_enqueue_script('admin-main');
    }
}
