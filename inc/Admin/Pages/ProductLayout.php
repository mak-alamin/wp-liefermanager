<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class ProductLayout
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);

        // Generate Custom Columns
        add_filter('manage_wp-liefer-pr-layouts_posts_columns', [$this, 'custom_post_type_columns']);

        add_action('manage_wp-liefer-pr-layouts_posts_custom_column', [$this, 'custom_post_type_column_data'], 10, 2);
    }

    function generate_options_with_carbon_fields()
    {
        $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;

        $layoutTypeSettings = array(
            Field::make('select', 'wp_liefer_product_layout_type', 'Wählen Sie den Produktlayouttyp aus')
                ->set_classes('fit-content')
                ->set_options(array(
                    'grid' => 'Raster',
                    'list' => 'Liste'
                )),
        );

        if ($post_id) {
            $layoutTypeSettings[] =  Field::make('text', 'wp_liefer_product_layout_shortcode', 'Shortcode')
                ->set_classes('fit-content')
                ->set_attributes(array('readOnly' => true))
                ->set_default_value('[wpliefermanager id="' . $post_id . '"]');
        }

        Container::make('post_meta', __('Layouttyp', 'wp-liefermanager'))
            ->where('post_type', '=', 'wp-liefer-pr-layouts')
            ->add_fields($layoutTypeSettings);
    }

    // Add custom column to wp-liefer-pr-layouts post type
    function custom_post_type_columns($columns)
    {
        $columns['shortcode'] = 'Shortcode';

        // Remove the date column
        unset($columns['date']);

        // Add the date column back after the shortcode column
        $columns['date'] = __('Datum', 'wp-liefermanager');

        return $columns;
    }

    // Populate custom column with shortcode
    function custom_post_type_column_data($column, $post_id)
    {
        if ('shortcode' === $column) {
            $shortcode = '[wpliefermanager id="' . $post_id . '"]';
            echo $shortcode;
        }
    }
}
