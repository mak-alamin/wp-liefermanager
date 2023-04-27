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

        add_action('woocommerce_variation_options_pricing', [$this, 'add_variation_checkboxes'], 10, 3);
        add_action('woocommerce_save_product_variation', [$this, 'save_variation_checkboxes'], 10, 2);
    }

    // Add extras for variable products
    function add_variation_checkboxes($loop, $variation_data, $variation)
    {
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'wp-liefer-extras',
        );

        $global_extras_query = get_posts($args);

        if (!empty($global_extras_query)) {
            foreach ($global_extras_query as $ext_key => $extra) {
                $useInVariation = carbon_get_post_meta($extra->ID, 'wp_liefer_use_in_variations');

                if ($useInVariation) {
                    $extraId = 'food_variation_extra[' . $variation->ID . '][' . $extra->ID . ']';

                    $extraChoosed = get_post_meta($variation->ID, 'food_variation_extra_' . $variation->ID . '_' . $extra->ID, true);

                    // Add the checkbox fields
                    woocommerce_wp_checkbox(array(
                        'id'            => $extraId,
                        'label'         => __($extra->post_title, 'wp-liefermanager'),
                        'description'   => __('', 'wp-liefermanager'),
                        'value'         => $extraChoosed,
                        'wrapper_class' => 'form-row',
                    ));
                }
            }
        }
    }


    function save_variation_checkboxes($variation_id, $i)
    {
        $variation_extras = $_POST['food_variation_extra'];

        foreach ($variation_extras as $var_id => $extras) {
            foreach ($extras as $ext_id => $extra) {

                $value = isset($_POST['food_variation_extra'][$var_id][$ext_id]) ? 'yes' : 'no';

                update_post_meta($variation_id, 'food_variation_extra_' . $var_id . '_' . $ext_id, $value);
            }
        }
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
