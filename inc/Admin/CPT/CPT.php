<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\CPT;

class CPT
{
    public function register()
    {
        add_action('init', [$this, 'create_table_cpt']);
        add_action('init', [$this, 'create_product_layout_cpt']);
        add_action('init', [$this, 'create_food_extra_cpt']);
    }

    function create_food_extra_cpt()
    {
        $labels = array(
            'name' => _x('Extras', 'wp-liefermanager'),
            'singular_name' => _x('Extra', 'wp-liefermanager'),
            'menu_name' => _x('Extras', 'wp-liefermanager'),
            'name_admin_bar' => _x('Extra', 'wp-liefermanager'),
            'archives' => __('Extra Archives', 'wp-liefermanager'),
            'attributes' => __('Extra Attributes', 'wp-liefermanager'),
            'parent_item_colon' => __('Parent Extra:', 'wp-liefermanager'),
            'all_items' => __('All Extras', 'wp-liefermanager'),
            'add_new_item' => __('Neue Extra hinzufügen', 'wp-liefermanager'),
            'add_new' => __('Neue Extra', 'wp-liefermanager'),
            'new_item' => __('Neue Extra', 'wp-liefermanager'),
            'edit_item' => __('Edit Extra', 'wp-liefermanager'),
            'update_item' => __('Update Extra', 'wp-liefermanager'),
            'view_item' => __('Ansehen Extra', 'wp-liefermanager'),
            'view_items' => __('Ansehen Extras', 'wp-liefermanager'),
            'search_items' => __('Extra suchen', 'wp-liefermanager'),
            'not_found' => __('Not found', 'wp-liefermanager'),
            'not_found_in_trash' => __('Not found in Trash', 'wp-liefermanager'),
            'featured_image' => __('Featured Image', 'wp-liefermanager'),
            'set_featured_image' => __('Set featured image', 'wp-liefermanager'),
            'remove_featured_image' => __('Remove featured image', 'wp-liefermanager'),
            'use_featured_image' => __('Use as featured image', 'wp-liefermanager'),
            'insert_into_item' => __('Insert into Extra', 'wp-liefermanager'),
            'uploaded_to_this_item' => __('Uploaded to this Extra', 'wp-liefermanager'),
            'items_list' => __('Extras list', 'wp-liefermanager'),
            'items_list_navigation' => __('Extras list navigation', 'wp-liefermanager'),
            'filter_items_list' => __('Filter Extras list', 'wp-liefermanager'),
        );
        $args = array(
            'label' => __('Extra', 'wp-liefermanager'),
            'description' => __('', 'wp-liefermanager'),
            'labels' => $labels,
            'menu_icon' => '',
            'supports' => array('title'),
            'taxonomies' => array(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 100,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'exclude_from_search' => false,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type('wp-liefer-extras', $args);
    }

    function create_table_cpt()
    {
        $labels = array(
            'name' => _x('Tische', 'wp-liefermanager'),
            'singular_name' => _x('Tisch', 'wp-liefermanager'),
            'menu_name' => _x('Tischbestellung', 'wp-liefermanager'),
            'name_admin_bar' => _x('Tische', 'wp-liefermanager'),
            'archives' => __('Tische Archives', 'wp-liefermanager'),
            'attributes' => __('Tische Attributes', 'wp-liefermanager'),
            'parent_item_colon' => __('Parent Tische:', 'wp-liefermanager'),
            'all_items' => __('All Tisch', 'wp-liefermanager'),
            'add_new_item' => __('Neue Tisch hinzufügen', 'wp-liefermanager'),
            'add_new' => __('Neue Tisch', 'wp-liefermanager'),
            'new_item' => __('Neue Tisch', 'wp-liefermanager'),
            'edit_item' => __('Edit Tisch', 'wp-liefermanager'),
            'update_item' => __('Update Tisch', 'wp-liefermanager'),
            'view_item' => __('Ansehen Tisch', 'wp-liefermanager'),
            'view_items' => __('Ansehen Tische', 'wp-liefermanager'),
            'search_items' => __('Tische suchen', 'wp-liefermanager'),
            'not_found' => __('Not found', 'wp-liefermanager'),
            'not_found_in_trash' => __('Not found in Trash', 'wp-liefermanager'),
            'featured_image' => __('Featured Image', 'wp-liefermanager'),
            'set_featured_image' => __('Set featured image', 'wp-liefermanager'),
            'remove_featured_image' => __('Remove featured image', 'wp-liefermanager'),
            'use_featured_image' => __('Use as featured image', 'wp-liefermanager'),
            'insert_into_item' => __('Insert into Tische', 'wp-liefermanager'),
            'uploaded_to_this_item' => __('Uploaded to this Tische', 'wp-liefermanager'),
            'items_list' => __('Tisch list', 'wp-liefermanager'),
            'items_list_navigation' => __('Tisch list navigation', 'wp-liefermanager'),
            'filter_items_list' => __('Filter Tisch list', 'wp-liefermanager'),
        );
        $args = array(
            'label' => __('Tischbestellung', 'wp-liefermanager'),
            'description' => __('', 'wp-liefermanager'),
            'labels' => $labels,
            'menu_icon' => '',
            'supports' => array('title'),
            'taxonomies' => array(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 100,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'exclude_from_search' => false,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type('wp-liefer-tables', $args);
    }

    function create_product_layout_cpt()
    {
        $labels = array(
            'name' => _x('Produkt Layouts', 'wp-liefermanager'),
            'singular_name' => _x('Layout', 'wp-liefermanager'),
            'menu_name' => _x('Produkt Layout', 'wp-liefermanager'),
            'name_admin_bar' => _x('Produkt Layout', 'wp-liefermanager'),
            'archives' => __('Layout Archives', 'wp-liefermanager'),
            'attributes' => __('Layout Attributes', 'wp-liefermanager'),
            'parent_item_colon' => __('Parent Layout:', 'wp-liefermanager'),
            'all_items' => __('All Layout', 'wp-liefermanager'),
            'add_new_item' => __('Neue Layout hinzufügen', 'wp-liefermanager'),
            'add_new' => __('Neue Layout', 'wp-liefermanager'),
            'new_item' => __('Neue Layout', 'wp-liefermanager'),
            'edit_item' => __('Edit Layout', 'wp-liefermanager'),
            'update_item' => __('Update Layout', 'wp-liefermanager'),
            'view_item' => __('Ansehen Layout', 'wp-liefermanager'),
            'view_items' => __('Ansehen Layout', 'wp-liefermanager'),
            'search_items' => __('Layout suchen', 'wp-liefermanager'),
            'not_found' => __('Not found', 'wp-liefermanager'),
            'not_found_in_trash' => __('Not found in Trash', 'wp-liefermanager'),
            'featured_image' => __('Featured Image', 'wp-liefermanager'),
            'set_featured_image' => __('Set featured image', 'wp-liefermanager'),
            'remove_featured_image' => __('Remove featured image', 'wp-liefermanager'),
            'use_featured_image' => __('Use as featured image', 'wp-liefermanager'),
            'insert_into_item' => __('Insert into Layout', 'wp-liefermanager'),
            'uploaded_to_this_item' => __('Uploaded to this Layout', 'wp-liefermanager'),
            'items_list' => __('Layout list', 'wp-liefermanager'),
            'items_list_navigation' => __('Layout list navigation', 'wp-liefermanager'),
            'filter_items_list' => __('Filter Layout list', 'wp-liefermanager'),
        );
        $args = array(
            'label' => __('Produkt Layout', 'wp-liefermanager'),
            'description' => __('', 'wp-liefermanager'),
            'labels' => $labels,
            'menu_icon' => '',
            'supports' => array('title'),
            'taxonomies' => array(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 100,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'exclude_from_search' => false,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type('wp-liefer-pr-layouts', $args);
    }
}
