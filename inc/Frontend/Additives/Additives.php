<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Additives;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Additives
{
    public function register()
    {
        if (is_admin()) {
            return;
        }

        add_action('woocommerce_before_add_to_cart_button', [$this, 'show_additives']);
    }

    public function show_additives()
    {
        $all_allergenes = carbon_get_theme_option('wp_liefer_alergenes');

        $allergenes_keys = get_post_meta(get_the_ID(), 'allergene_checked', true);

        echo '<div class="additives">';

        if (!empty($allergenes_keys)) {
            echo '<h4 class="mt-3">Allergene:</h4>';

            echo '<ul >';

            foreach ($all_allergenes as $key => $allergene) {
                $allergene_key = strtolower(str_replace(' ', '_', $allergene['fullname']));

                if (in_array($allergene_key, $allergenes_keys)) {

                    $icon = wp_get_attachment_url($allergene['icon']);

                    echo ' <li>' . $allergene['fullname'] . '<img src="' . $icon . '" class="ml-1"/>' . '</li>';
                }
            }

            echo '</ul>';
        }

        $all_additives = carbon_get_theme_option('wp_liefer_additives');

        $additive_keys = get_post_meta(get_the_ID(), 'additives_checked', true);

        if (!empty($additive_keys)) {
            echo '<h4>' . _x('Zusatzstoffe', 'wp-liefermanager') . ':</h4>';

            echo '<ul>';

            foreach ($all_additives as $key => $additive) {
                $additive_key = strtolower(str_replace(' ', '_', $additive['fullname']));

                if (in_array($additive_key, $additive_keys)) {
                    $icon = wp_get_attachment_url($allergene['icon']);

                    echo ' <li>' . $additive['fullname'] . '<img src="' . $icon . '" class="ml-1"/>' . '</li>';
                }
            }

            echo '</ul>';
        }

        echo '</div>';
    }
}
