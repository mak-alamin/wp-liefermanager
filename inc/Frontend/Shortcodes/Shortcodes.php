<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Shortcodes;

// [lebensmittel_typ]

class Shortcodes
{
    public function register()
    {
        add_shortcode('zusatzstoffe', [$this, 'generate_additives_shortcode']);

        add_shortcode('allergene', [$this, 'generate_allergenes_shortcode']);
    }

    public function generate_additives_shortcode()
    {
        $additives = carbon_get_theme_option('wp_liefer_additives');

        if (empty($additives)) {
            return;
        }

        echo '<table class="additives">';

        foreach ($additives as $key => $additive) {

            $icon = wp_get_attachment_url($additive['icon']);

            echo '<tr>';

            echo '<td><img src="' . $icon . '" /></td>';

            echo '<td> <strong> ' . $additive['fullname'] . ' </strong>' . $additive['description'] . '</td>';

            echo '</tr>';
        }

        echo '</table>';
    }

    public function generate_allergenes_shortcode()
    {
        $allergenes = carbon_get_theme_option('wp_liefer_alergenes');

        if (empty($allergenes)) {
            return;
        }

        echo '<table class="additives">';

        foreach ($allergenes as $key => $allergene) {

            $icon = wp_get_attachment_url($allergene['icon']);

            echo '<tr>';

            echo '<td><img src="' . $icon . '" /></td>';

            echo '<td> <strong> ' . $allergene['fullname'] . ' </strong>' . $allergene['description'] . '</td>';

            echo '</tr>';
        }

        echo '</table>';
    }
}
