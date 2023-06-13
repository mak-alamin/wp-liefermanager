<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Inc\Base\Common;

class Branches extends Common
{
    public function register()
    {
        add_action('carbon_fields_register_fields', array($this, 'add_branch_fields'));

        add_action('init', array($this, 'create_branches_taxonomy'));
    }

    public function add_branch_fields()
    {
        $opening_hours = $this->generate_opening_hours();

        $max_opening_hours_orders = Field::make('text', 'max_opening_hours_orders', 'Maximale Bestellmenge während der Öffnungszeiten');

        $max_delivery_times_orders = Field::make('text', 'max_delivery_times_orders', 'Maximale Bestellmenge während der Lieferzeiten');

        array_push($opening_hours['opening_hours'], $max_opening_hours_orders );
        
        array_push($opening_hours['delivery_times'], $max_delivery_times_orders );

        Container::make('term_meta', __('Filialinfo'))
            ->where('term_taxonomy', '=', 'wp_liefer_branches') 
            ->add_tab(__('Filialinfo', 'wp-liefermanager'), array(
                Field::make('text', 'branch_address', __('Anschrift der Filiale')),
                Field::make('text', 'branch_email', __('Filial-E-Mail')),
                Field::make('text', 'delivery_cost', __('Lieferkosten')),
                Field::make('text', 'min_order_value', __('Mindestbestellwert erforderlich')),
                Field::make('text', 'min_order_value_free_shipping', __('Mindestbestellwert für kostenlosen Versand')),
                Field::make('textarea', 'zipcodes', __('Postleitzahlen')),
            ))
             // Opening Hours
            ->add_tab(__('Öffnungszeiten', 'wp-liefermanager'), $opening_hours['opening_hours'])

            //Delivery Times
            ->add_tab(__('Lieferzeiten', 'wp-liefermanager'), $opening_hours['delivery_times']);
    }

    public function create_branches_taxonomy()
    {
        $labels = array(
            'name' => __('Filialen'),
            'singular_name' => __('Filiale'),
            'menu_name' => __('Filialen'),
        );

        $labels = array(
            'name'                       => _x( 'Filialen', 'wp-liefermanager' ),
            'singular_name'              => _x( 'Filiale', 'wp-liefermanager' ),
            'search_items'               => __( 'Filialen durchsuchen', 'wp-liefermanager' ),
            'popular_items'              => __( 'Beliebte Filialen', 'wp-liefermanager' ),
            'all_items'                  => __( 'Alle Filialen', 'wp-liefermanager' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Filiale bearbeiten', 'wp-liefermanager' ),
            'update_item'                => __( 'Filiale aktualisieren', 'wp-liefermanager' ),
            'add_new_item'               => __( 'Neue Filiale hinzufügen', 'wp-liefermanager' ),
            'new_item_name'              => __( 'Neuer Filialname', 'wp-liefermanager' ),
            'separate_items_with_commas' => __( 'Trennen Sie Filialen durch Kommas', 'wp-liefermanager' ),
            'add_or_remove_items'        => __( 'Filialen hinzufügen oder entfernen', 'wp-liefermanager' ),
            'choose_from_most_used'      => __( 'Wählen Sie aus den am häufigsten genutzten Filialen', 'wp-liefermanager' ),
            'not_found'                  => __( 'Keine Filialen gefunden.', 'wp-liefermanager' ),
            'menu_name'                  => __( 'Filiales', 'wp-liefermanager' ),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'wp-liefer-branches'),
        );

        register_taxonomy('wp_liefer_branches', 'product', $args);
    }
}
