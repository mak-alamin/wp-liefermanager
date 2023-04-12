<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Additives
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);
    }

    function generate_options_with_carbon_fields()
    {
        Container::make('theme_options', __('Zusatzstoffe'))
            ->set_page_parent('wp-liefermanager')
            ->set_page_file('wp-liefermanager-additives')

            // Allergene
            ->add_tab(__('Allergene', 'wp-liefermanager'), array(
                Field::make('complex', 'wp_liefer_alergenes', __('Allergene', 'wp-liefermanager'))
                    ->add_fields('allergene', array(
                        Field::make('text', 'short_name', 'Kurzer Name'),
                        Field::make('text', 'fullname', 'Name'),
                        Field::make('textarea', 'description', 'Description'),
                        Field::make('file', 'icon', 'Icon')
                            ->set_type(array('image')),
                    ))
            ))

            // Additives
            ->add_tab(__('Zusatzstoffe', 'wp-liefermanager'), array(
                Field::make('complex', 'wp_liefer_additives', __('Zusatzstoffe', 'wp-liefermanager'))
                    ->add_fields('additives', array(
                        Field::make('text', 'short_name', 'Kurzer Name'),
                        Field::make('text', 'fullname', 'Name'),
                        Field::make('textarea', 'description', 'Description'),
                        Field::make('file', 'icon', 'Icon')
                            ->set_type(array('image')),
                    ))
            ))

            // Food Types
            ->add_tab(__('Lebensmitteltyp', 'wp-liefermanager'), array(
                Field::make('complex', 'wp_liefer_food_types', __('Lebensmittel Typ', 'wp-liefermanager'))
                    ->add_fields('food_types', array(
                        Field::make('text', 'short_name', 'Kurzer Name'),
                        Field::make('text', 'fullname', 'Name'),
                        Field::make('select', 'mildness', 'Milde')
                            ->add_options([$this, 'get_food_type_mildness']),
                        Field::make('select', 'others', 'Andere')
                            ->add_options([$this, 'get_food_type_others']),
                    )),
            ))

            // Icons
            ->add_tab(__('Icons', 'wp-liefermanager'), array(
                Field::make('complex', 'wp_liefer_mildness_foodtype', __('Milde', 'wp-liefermanager'))
                    ->add_fields('mildness_foodtype', array(
                        Field::make('text', 'short_name', 'Kurzer Name'),
                        Field::make('text', 'fullname', 'Name'),
                        Field::make('file', 'icon', 'Icon'),
                    )),

                Field::make('separator', 'wp_liefer_food_icon_seperator1', ''),

                Field::make('complex', 'wp_liefer_others_foodtype', __('Andere', 'wp-liefermanager'))
                    ->add_fields('others_foodtype', array(
                        Field::make('text', 'short_name', 'Kurzer Name'),
                        Field::make('text', 'fullname', 'Name'),
                        Field::make('file', 'icon', 'Icon')
                            ->set_type(array('image')),
                    ))
            ));
    }

    public function get_food_type_mildness()
    {
        $foodtype_mildness = carbon_get_theme_option('wp_liefer_mildness_foodtype');

        $options = array();

        if (empty($foodtype_mildness)) {
            return $options;
        }

        foreach ($foodtype_mildness as $key => $mildness) {
            $options[strtolower($mildness['fullname'])] = $mildness['fullname'];
        }

        return $options;
    }

    public function get_food_type_others()
    {
        $foodtype_others = carbon_get_theme_option('wp_liefer_others_foodtype');

        $options = array();

        if (empty($foodtype_others)) {
            return $options;
        }

        foreach ($foodtype_others as $key => $value) {
            $options[strtolower($value['fullname'])] = $value['fullname'];
        }

        return $options;
    }
}
