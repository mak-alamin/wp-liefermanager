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
            ->where('post_type', '=', 'wp-liefer-extras')
            ->add_fields(array(
                Field::make('complex', 'wp_liefer_global_extras', '')
                    ->set_classes('wp_liefer_food_extras')
                    ->add_fields('wp_liefer_global_extras', 'Zusatzoption', array(
                        Field::make('text', 'option_name', 'Gericht'),
                        Field::make('select', 'option_type', 'Option Typ')
                            ->add_options(array(
                                'checkbox' => 'Checkbox',
                                'radio' => 'Radio Buttons',
                                'select' => 'Select Box',
                                'text' => 'Text Box',
                                'textarea' => 'Textarea'
                            )),
                        Field::make('select', 'required', 'Erforderlich?')
                            ->add_options(array(
                                'yes' => 'Ja',
                                'no' => 'Nein',
                            )),
                        Field::make('complex', 'extra_options', 'Optionen')
                            ->set_classes('wp_liefer_food_extra_options')
                            ->add_fields('global_extra_options', 'Option', array(
                                Field::make('text', 'option_name', 'Option Name'),
                                Field::make('checkbox', 'disable', 'Disable?'),
                                Field::make('text', 'option_price', 'Preis'),
                                Field::make('select', 'price_type', 'Art des Preis')
                                    ->add_options(array(
                                        'quantity' => 'Mengenbasiert',
                                        'fixed' => 'Fester Betrag',
                                    )),
                            ))
                    ))
            ));
    }
}
