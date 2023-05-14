<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Shortcodes;

class Shortcodes
{
    public function register()
    {
        add_shortcode('zusatzstoffe', [$this, 'generate_additives_shortcode']);

        add_shortcode('allergene', [$this, 'generate_allergenes_shortcode']);

        add_shortcode('lebensmittel_typ', [$this, 'generate_foodtype_shortcode']);
    }

    public function generate_shortcode_table($options)
    {
        if (empty($options)) {
            return '';
        }

        $html = '';

        $html .= '<div class="additives">';
        $html .= '<table>';

        foreach ($options as $key => $option) {

            $icon = isset($option['icon']) ? wp_get_attachment_url($option['icon']) : '';

            $description = isset($option['description']) ? $option['description'] : '';

            $html .= '<tr>';

            $html .= '<td><img src="' . $icon . '" /></td>';

            $html .= '<td> <strong> ' . $option['fullname'] . ' </strong>' . $description . '</td>';

            $html .= '</tr>';
        }

        $html .= '</table>';

        $html .= '</div>';

        return $html;
    }

    public function generate_foodtype_shortcode()
    {
        $food_types = carbon_get_theme_option('wp_liefer_mildness_foodtype');

        return $this->generate_shortcode_table($food_types);
    }

    public function generate_additives_shortcode()
    {
        $additives = carbon_get_theme_option('wp_liefer_additives');

        return $this->generate_shortcode_table($additives);
    }

    public function generate_allergenes_shortcode()
    {
        $allergenes = carbon_get_theme_option('wp_liefer_alergenes');

        return $this->generate_shortcode_table($allergenes);
    }
}
