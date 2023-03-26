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
                    Field::make('time', 'open_at', __('Opens', 'wp-liefermanager')),
                    Field::make('time', 'close_at', __('Closes', 'wp-liefermanager'))
                ));

            $delivery_time_fields[] = Field::make('complex', 'wp_liefer_' . $slug . '_delivery_times', $weekday)
                ->add_fields(array(
                    Field::make('time', 'open_at', __('Opens', 'wp-liefermanager')),
                    Field::make('time', 'close_at', __('Closes', 'wp-liefermanager'))
                ));
        }

        Container::make('theme_options', __('Settings'))
            ->set_page_parent('wp-liefermanager')

            ->add_tab(__('Opening Hours', 'wp-liefermanager'), $opening_hour_fields)

            ->add_tab(__('Delivery Times'), $delivery_time_fields)

            ->add_tab(__('Tip'), array(
                Field::make('text', 'wp_liefer_tip_type', __('Tip Type', 'wp-liefermanager')),
            ));
    }
}
