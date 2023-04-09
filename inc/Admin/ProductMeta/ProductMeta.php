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
    }

    public function register_additives_metabox()
    {
        $allergenes = carbon_get_theme_option('wp_liefer_alergenes');
        $additives = carbon_get_theme_option('wp_liefer_additives');

        $allergene_checkboxes = array();
        $additives_checkboxes = array();

        foreach ($allergenes as $key => $allergene) {
            $allergene_checkboxes[strtolower(str_replace(' ', '_', $allergene['fullname']))] = $allergene['fullname'];
        }

        foreach ($additives as $key => $additive) {
            $additives_checkboxes[strtolower(str_replace(' ', '_', $additive['fullname']))] = $additive['fullname'];
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
    }
}
