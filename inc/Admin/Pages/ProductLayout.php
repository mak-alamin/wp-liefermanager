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

        add_action('init', array($this, 'getProductCategories'));
    }

    function getProductCategories()
    {
        $taxonomy     = 'product_cat';
        $orderby      = 'name';
        $show_count   = 0;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title        = '';
        $hide_empty   = 0;

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $hide_empty
        );
        $all_categories = get_categories($args);

        $product_cats = array();

        foreach ($all_categories as $category) {
            $product_cats[$category->term_id] = $category->name;
        }

        return $product_cats;
    }

    function generate_options_with_carbon_fields()
    {
        $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;

        // Layout Type
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

        // Layout settings
        $layoutSettings = array(
            Field::make('select', 'wp_liefer_layout_grid_column', 'Rasterspalte')
                ->set_classes('fit-content')
                ->set_options(array(
                    '1' => 'Spalte 1',
                    '2' => 'Spalte 2',
                    '3' => 'Spalte 3',
                    '4' => 'Spalte 4',
                    '5' => 'Spalte 5',
                    '6' => 'Spalte 6',
                )),

            Field::make('select', 'wp_liefer_layout_style', 'Stil')
                ->set_classes('fit-content')
                ->set_options(array(
                    '1' => 'Stil 1',
                    '2' => 'Stil 2',
                    '3' => 'Stil 3',
                )),

            Field::make('select', 'wp_liefer_product_sorting', 'Sortierung')
                ->set_classes('fit-content')
                ->set_options(array(
                    'date' => 'Datum',
                    'price' => 'Preis',
                    'menu_order' => 'Menüreihenfolge',
                )),

            Field::make('set', 'wp_liefer_layout_product_categories', 'Produktkategorien')
                ->set_classes('fit-content')
                ->set_options(array($this, 'getProductCategories')),

            Field::make('select', 'wp_liefer_layout_cat_title_view', 'Titelansicht der Produktkategorie')
                ->set_classes('fit-content')
                ->set_options(array(
                    'top_tabs' => 'Top Tabs',
                    'left_tabs' => 'Left Tabs',
                    'cat_titles' => 'Category Titles',
                    'no_titles' => 'No Titles or Tabs',
                )),
        );

        Container::make('post_meta', __('Layout-Einstellungen', 'wp-liefermanager'))
            ->where('post_type', '=', 'wp-liefer-pr-layouts')
            ->add_fields($layoutSettings);
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
            $shortcode = '[wpliefermanager layout_id="' . $post_id . '"]';
            echo $shortcode;
        }
    }
}
