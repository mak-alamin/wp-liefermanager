<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Base;

class Common
{
    public function show_short_description($post_id)
    {
        $short_description = get_post_field('post_excerpt', $post_id);

        if (empty($short_description)) {
            $short_description = get_the_excerpt($post_id);
        }

        echo $short_description;
    }

    public function show_additives($product_id)
    {
        // Allergenes
        $all_allergenes = carbon_get_theme_option('wp_liefer_alergenes');

        $allergenes_keys = get_post_meta(intval($product_id), 'allergene_checked', true);

        echo '<div class="additives">';

        if (!empty($allergenes_keys)) {
            echo '<ul >';

            foreach ($all_allergenes as $key => $allergene) {
                $allergene_key = strtolower(str_replace(' ', '_', $allergene['fullname']));

                if (in_array($allergene_key, $allergenes_keys)) {

                    $icon = isset($allergene['icon']) ?  wp_get_attachment_url($allergene['icon']) : '';

                    // echo ' <li>' . $allergene['fullname'] . '<img src="' . $icon . '" class="ml-1"/>' . '</li>';

                    echo ' <li><a href="' . site_url('zusatzstoffe-allergene') . '" target="_blank"> <img src="' . $icon . '" class="ml-1"/></a></li>';
                }
            }

            echo '</ul>';
        }

        // Additives
        $all_additives = carbon_get_theme_option('wp_liefer_additives');

        $additive_keys = get_post_meta(intval($product_id), 'additives_checked', true);

        if (!empty($additive_keys)) {
            echo '<ul>';

            foreach ($all_additives as $key => $additive) {
                $additive_key = strtolower(str_replace(' ', '_', $additive['fullname']));

                if (in_array($additive_key, $additive_keys)) {
                    $icon = isset($additive['icon']) ?  wp_get_attachment_url($additive['icon']) : '';

                    echo ' <li><a href="' . site_url('zusatzstoffe-allergene') . '" target="_blank"> <img src="' . $icon . '" class="ml-1"/></a></li>';
                }
            }

            echo '</ul>';
        }

        // Food Types
        $all_foodtype_icons = carbon_get_theme_option('wp_liefer_mildness_foodtype');

        $foodtype_keys = get_post_meta(intval($product_id), 'foodtypes_checked', true);

        if (!empty($foodtype_keys)) {
            echo '<ul>';

            foreach ($all_foodtype_icons as $key => $foodtype) {
                $foodtype_key = strtolower(str_replace(' ', '_', $foodtype['fullname']));

                if (in_array($foodtype_key, $foodtype_keys)) {
                    $icon = isset($foodtype['icon']) ?  wp_get_attachment_url($foodtype['icon']) : '';

                    echo ' <li><a href="' . site_url('zusatzstoffe-allergene') . '" target="_blank"> <img src="' . $icon . '" class="ml-1"/></a></li>';
                }
            }
            echo '</ul>';
        }

        echo '</div>';
    }
}
