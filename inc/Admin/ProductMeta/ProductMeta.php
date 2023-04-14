<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\ProductMeta;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class ProductMeta
{
    public function register()
    {
        add_action('cmb2_admin_init', array($this, 'register_additives_metabox'));

        add_action('carbon_fields_register_fields', [$this, 'generate_extra_options_metabox']);
    }

    /*
     *-------------------------------------
     * Extra Options Meta box
     *-------------------------------------
     */
    function generate_extra_options_metabox()
    {
        $args = array(
            'post_type' => 'wp-liefer-extras',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $global_extras_query = get_posts($args);

        $global_extras = array();

        if (!empty($global_extras_query)) {
            foreach ($global_extras_query as $key => $extra) {
                $global_extras[$extra->ID] = $extra->post_title;
            }
        }

        Container::make('post_meta', __('Zusätzliche Optionen', 'wp-liefermanager'))
            ->where('post_type', '=', 'product')

            ->add_tab('Globale Extras', array(
                Field::make('set', 'global_extras', __('Wählen Sie aus globalen Extras'))
                    ->set_options($global_extras),
            ))

            ->add_tab(_x('Extras hinzufügen', 'wp-liefermanager'), array(
                Field::make('complex', 'wp_liefer_product_extras', 'Extras hinzufügen')
                    ->set_classes('wp_liefer_food_extras')
                    ->add_fields('wp_liefer_product_extras', 'Zusatzoption', array(
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
                            ->add_fields('product_extra_options', 'Option', array(
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

    /*
     *-------------------------------------
     * Additives Meta box
     *-------------------------------------
     */
    public function register_additives_metabox()
    {
        $allergenes = carbon_get_theme_option('wp_liefer_alergenes');
        $additives = carbon_get_theme_option('wp_liefer_additives');
        $food_types = carbon_get_theme_option('wp_liefer_mildness_foodtype');

        $allergene_checkboxes = array();
        $additives_checkboxes = array();
        $food_types_checkboxes = array();

        foreach ($allergenes as $key => $allergene) {
            $allergene_checkboxes[strtolower(str_replace(' ', '_', $allergene['fullname']))] = $allergene['fullname'];
        }

        foreach ($additives as $key => $additive) {
            $additives_checkboxes[strtolower(str_replace(' ', '_', $additive['fullname']))] = $additive['fullname'];
        }

        foreach ($food_types as $key => $food_type) {
            $food_types_checkboxes[strtolower(str_replace(' ', '_', $food_type['fullname']))] = $food_type['fullname'];
        }

        $cmb2 = new_cmb2_box(array(
            'id'            => 'additives_metabox',
            'title'         => esc_html__('Zusatzstoffe', 'wp-liefermanager'),
            'object_types'  => array('product'),
        ));

        $cmb2->add_field(array(
            'name'    => _x('Allergene', 'wp-liefermanager'),
            'desc'    => '',
            'id'      => 'allergene_checked',
            'type'    => 'multicheck',
            'options' => $allergene_checkboxes,
        ));

        $cmb2->add_field(array(
            'name'    => _x('Zusatzstoffe', 'wp-liefermanager'),
            'desc'    => '',
            'id'      => 'additives_checked',
            'type'    => 'multicheck',
            'options' => $additives_checkboxes,
        ));

        $cmb2->add_field(array(
            'name'    => _x('Lebensmitteltyp', 'wp-liefermanager'),
            'desc'    => '',
            'id'      => 'foodtypes_checked',
            'type'    => 'multicheck',
            'options' => $food_types_checkboxes,
        ));
    }
}
