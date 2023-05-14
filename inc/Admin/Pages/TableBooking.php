<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class TableBooking
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);

        add_action('add_meta_boxes', function () {
            add_meta_box('qr_code', 'QR Code', array($this, 'qr_code_meta_box'), 'wp-liefer-tables', 'side');
        });

        add_action('save_post_wp-liefer-tables', array($this, 'generate_qrcode'));
    }

    function generate_qrcode($post_id)
    {
        global $post;

        // Check if the post is being saved as a new post.
        if (isset($post->post_type) && $post->post_type === 'wp-liefer-tables') {
            if (file_exists(WP_LIEFERMANAGER_PLUGIN_DIR . '/libs/phpqrcode/index.php')) {
                require_once WP_LIEFERMANAGER_PLUGIN_DIR . '/libs/phpqrcode/index.php';

                $table_data = $_POST['carbon_fields_compact_input'];

                $tableId = isset($table_data['_wp_liefer_table_id']) ? $table_data['_wp_liefer_table_id'] : 0;

                $product_url = isset($table_data['_wp_liefer_table_product_url']) ? $table_data['_wp_liefer_table_product_url'] : site_url();

                $data = array(
                    'table_product_url' => $product_url . '?table_id=' . $tableId
                );

                wp_liefer_generate_table_qr_code($post_id, $data);
            }
        }
    }


    function qr_code_meta_box()
    {
        $post_id = get_the_ID();

        $qr_code_image = '';

        $image_name = 'table_qr_code_' . $post_id . '.png';

        if (file_exists(WP_LIEFERMANAGER_PLUGIN_DIR . '/libs/phpqrcode/temp/' . $image_name)) {
            $qr_code_image = WP_LIEFERMANAGER_PLUGIN_URL . '/libs/phpqrcode/temp/' . $image_name;

            echo '<img src="' . $qr_code_image . '" />';

            echo '<p><a href="' . $qr_code_image . '" download="' . $image_name . '" class="button button-primary button-large">Download</a></p>';
        }
    }


    function generate_options_with_carbon_fields()
    {
        Container::make('post_meta', __('Tischinformationen', 'wp-liefermanager'))
            ->where('post_type', '=', 'wp-liefer-tables')
            ->add_fields(array(
                Field::make('text', 'wp_liefer_table_id', 'Tisch ID'),
                Field::make('text', 'wp_liefer_table_product_url', 'Tischprodukt URL'),
            ));
    }
}
