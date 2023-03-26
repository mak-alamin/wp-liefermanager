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
        add_action('carbon_fields_register_fields', [$this, 'attach_plugin_options']);
    }

    function attach_plugin_options()
    {
        Container::make('theme_options', __('Settings'))
            ->set_page_parent('wp-liefermanager')

            ->add_tab(__('Opening Hours'), array(
                Field::make('complex', 'wp_liefer_montag_opening_hours', __('Montag'))
                    ->add_fields(array(
                        Field::make('time', 'open_at', __('Opens', 'wp-liefermanager')) // ->set_time_format('HH')
                        ,
                        Field::make('time', 'close_at', __('Closes', 'wp-liefermanager')) // ->set_time_format('HH')
                    )),

                // Field::make('complex', 'wp_liefer_dienstag_opening_hours', __('Dienstag', 'wp-liefermanager'))
                //     ->add_fields(array(
                //         Field::make('time', 'open_at', __('Opens', 'wp-liefermanager'))->set_time_format('HH'),
                //         Field::make('time', 'close_at', __('Closes', 'wp-liefermanager'))->set_time_format('HH'),
                //     ))
            ))

            ->add_tab(__('Delivery Times'), array(
                Field::make('text', 'crb_delivery_times', __('Add Time', 'wp-liefermanager')),
            ))

            ->add_tab(__('Tip'), array(
                Field::make('text', 'crb_tip_type', __('Tip Type', 'wp-liefermanager')),
            ));
    }
}
