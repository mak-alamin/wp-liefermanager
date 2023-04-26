<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Settings
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);
    }

    function generate_options_with_carbon_fields()
    {
        $weekdays = array(
            'montag' => __('Montag', 'wp-liefermanager'),
            'dienstag' => __('Dienstag', 'wp-liefermanager'),
            'mittwoch' => __('Mittwoch', 'wp-liefermanager'),
            'donnerstag' => __('Donnerstag', 'wp-liefermanager'),
            'freitag' => __('Freitag', 'wp-liefermanager')
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

        Container::make('theme_options', __('Settings'))
            ->set_page_file('wp-liefermanager-settings')
            ->set_page_parent('wp-liefermanager')

            // Opening Hours
            ->add_tab(__('Öffnungszeiten', 'wp-liefermanager'), $opening_hour_fields)

            //Delivery Times
            ->add_tab(__('Lieferzeiten'), $delivery_time_fields)

            // Tips
            ->add_tab(__('Trinkgeld'), array(

                Field::make('checkbox', 'wp_liefer_tip_active', __('Aktivieren Trinkgeld', 'wp-liefermanager')),

                Field::make('text', 'wp_liefer_tip_label', __('Label Trinkgeld', 'wp-liefermanager')),

                Field::make('select', 'wp_liefer_tip_type', __('Tipptyp', 'wp-liefermanager'))
                    ->set_options(array(
                        'fixed' => __('Fester Betrag', 'wp-liefermanager'),
                        'percent' => __('Prozent', 'wp-liefermanager'),
                        'both' => __('Beide', 'wp-liefermanager')
                    )),

                Field::make('text', 'wp_liefer_tip_default_value', __('Default Value', 'wp-liefermanager')),

                Field::make('complex', 'wp_liefer_tip_custom_percents', __('Benutzerdefinierte Spitze Prozentsatz', 'wp-liefermanager'))
                    ->set_conditional_logic(array(
                        array(
                            'field' => 'wp_liefer_tip_type',
                            'value' => 'fixed',
                            'compare' => '!=',
                        )
                    ))
                    ->set_classes('wp_liefer_custom_percents')
                    ->add_fields(
                        array(
                            Field::make('text', 'wp_liefer_custom_percent', '')
                                ->set_attribute('type', 'number')->set_attribute('placeholder', 'Tippprozent hinzufügen'),
                        )
                    ),
            ));
    }
}
