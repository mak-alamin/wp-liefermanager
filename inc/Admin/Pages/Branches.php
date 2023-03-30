<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Branches
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);
    }

    function generate_options_with_carbon_fields()
    {
        Container::make('theme_options', __('Filialen'))
            ->set_page_file('wp-liefermanager-branches')
            ->set_page_parent('wp-liefermanager');
    }
}
