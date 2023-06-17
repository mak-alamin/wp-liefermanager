<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Base;

use Carbon_Fields\Field;

class Common
{
    public function get_branchId()
    {
        $selectedBranch = isset($_COOKIE['wp_liefer_selected_branch']) ? $_COOKIE['wp_liefer_selected_branch'] : null;

        $branchInfo = $this->get_branchInfo($selectedBranch);

        $branchId = empty($branchInfo) ? 0 : $branchInfo->id;

        return $branchId;
    }

    public function get_branchInfo($selectedBranch)
    {
        if (empty($selectedBranch)) {
            return null;
        }

        $formattedData = str_replace('\"', '"', $selectedBranch);

        $branchInfo = json_decode($formattedData);

        return $branchInfo;
    }

    public function generate_opening_hours()
    {
        $weekdays = array(
            'montag' => __('Montag', 'wp-liefermanager'),
            'dienstag' => __('Dienstag', 'wp-liefermanager'),
            'mittwoch' => __('Mittwoch', 'wp-liefermanager'),
            'donnerstag' => __('Donnerstag', 'wp-liefermanager'),
            'freitag' => __('Freitag', 'wp-liefermanager'),
            'samstag' => __('Samstag', 'wp-liefermanager'),
            'sontag' => __('Sontag', 'wp-liefermanager'),
        );

        $opening_hour_fields = array();
        $delivery_time_fields = array();

        foreach ($weekdays as $slug => $weekday) {
            $opening_hour_fields[] = Field::make('complex', 'wp_liefer_' . $slug . '_opening_hours', $weekday)
                ->add_fields(array(
                    Field::make('time', 'open_at', __('Geöffnet', 'wp-liefermanager')),
                    Field::make('time', 'close_at', __('Geschlossen', 'wp-liefermanager')),
                ));

            $delivery_time_fields[] = Field::make('complex', 'wp_liefer_' . $slug . '_delivery_times', $weekday)
                ->add_fields(array(
                    Field::make('time', 'open_at', __('Geöffnet', 'wp-liefermanager')),
                    Field::make('time', 'close_at', __('Geschlossen', 'wp-liefermanager'))
                ));
        }

        return array(
            'opening_hours' => $opening_hour_fields,
            'delivery_times' => $delivery_time_fields
        );
    }

    public function get_products($category_id = 0, $branch_id = 0)
    {
        $tax_query = array();

        if ($category_id) {
            $tax_query[] =
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => intval($category_id),
                );
        }

        if ($branch_id) {
            $tax_query[] =
                array(
                    'taxonomy' => 'wp_liefer_branches',
                    'field' => 'term_id',
                    'terms' => intval($branch_id),
                );
        }

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => $tax_query
        );

        $products = get_posts($args);

        return $products;
    }

    function generate_add_to_cart_button($product_id)
    {
        $product = wc_get_product($product_id);

        $button_text = __('Bestellen', 'wp-liefermanager');

        $button_url = get_permalink($product->get_id());

        // if ($product->is_type('variable')) {
        // } else {
        //     $button_url = esc_url($product->add_to_cart_url());
        // }

        $tableInfo = (isset($_GET['table_id'])) ? 'data-table_id=' . $_GET['table_id'] : '';

        return '<a href="' . $button_url . '" ' . $tableInfo . ' class="button add_to_cart">' . $button_text . '</a>';
    }

    public function show_short_description($post_id)
    {
        $short_description = get_post_field('post_excerpt', $post_id);

        if (empty($short_description)) {
            $short_description = get_the_excerpt($post_id);
        }

        return $short_description;
    }

    public function show_additives($product_id)
    {
        // Allergenes
        $all_allergenes = carbon_get_theme_option('wp_liefer_alergenes');

        $allergenes_keys = get_post_meta(intval($product_id), 'allergene_checked', true);

        $html = '';

        $html .= '<div class="additives">';

        if (!empty($allergenes_keys)) {
            $html .= '<ul >';

            foreach ($all_allergenes as $key => $allergene) {
                $allergene_key = strtolower(str_replace(' ', '_', $allergene['fullname']));

                if (in_array($allergene_key, $allergenes_keys)) {

                    $icon = isset($allergene['icon']) ?  wp_get_attachment_url($allergene['icon']) : '';

                    // $html .= ' <li>' . $allergene['fullname'] . '<img src="' . $icon . '" class="ml-1"/>' . '</li>';

                    $html .= ' <li><a href="' . site_url('zusatzstoffe-allergene') . '" target="_blank"> <img src="' . $icon . '" class="ml-1"/></a></li>';
                }
            }

            $html .= '</ul>';
        }

        // Additives
        $all_additives = carbon_get_theme_option('wp_liefer_additives');

        $additive_keys = get_post_meta(intval($product_id), 'additives_checked', true);

        if (!empty($additive_keys)) {
            $html .= '<ul>';

            foreach ($all_additives as $key => $additive) {
                $additive_key = strtolower(str_replace(' ', '_', $additive['fullname']));

                if (in_array($additive_key, $additive_keys)) {
                    $icon = isset($additive['icon']) ?  wp_get_attachment_url($additive['icon']) : '';

                    $html .= ' <li><a href="' . site_url('zusatzstoffe-allergene') . '" target="_blank"> <img src="' . $icon . '" class="ml-1"/></a></li>';
                }
            }

            $html .= '</ul>';
        }

        // Food Types
        $all_foodtype_icons = carbon_get_theme_option('wp_liefer_mildness_foodtype');

        $foodtype_keys = get_post_meta(intval($product_id), 'foodtypes_checked', true);

        if (!empty($foodtype_keys)) {
            $html .= '<ul>';

            foreach ($all_foodtype_icons as $key => $foodtype) {
                $foodtype_key = strtolower(str_replace(' ', '_', $foodtype['fullname']));

                if (in_array($foodtype_key, $foodtype_keys)) {
                    $icon = isset($foodtype['icon']) ?  wp_get_attachment_url($foodtype['icon']) : '';

                    $html .= ' <li><a href="' . site_url('zusatzstoffe-allergene') . '" target="_blank"> <img src="' . $icon . '" class="ml-1"/></a></li>';
                }
            }
            $html .= '</ul>';
        }

        $html .= '</div>';

        return $html;
    }
}
