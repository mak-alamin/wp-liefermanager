<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Extras
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);
    }

    function generate_options_with_carbon_fields()
    {
        Container::make('post_meta', __('Zusätzliche Optionen', 'wp-liefermanager'))
            ->add_fields(array(
                Field::make('complex', 'wp_liefer_food_extras', '')
                    ->add_fields('wp_liefer_food_extras', 'Zusatzoption', array(
                        Field::make('text', 'extra_name', 'Gericht'),
                    ))
            ));
    }
}
