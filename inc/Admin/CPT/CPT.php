<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\CPT;

class CPT
{
    public function register()
    {
        add_action('init', [$this, 'create_food_extra_cpt']);
    }

    function create_food_extra_cpt()
    {
        $labels = array(
            'name' => _x('Extras', 'Post Type General Name', 'wp-liefermanager'),
            'singular_name' => _x('Extra', 'Post Type Singular Name', 'wp-liefermanager'),
            'menu_name' => _x('Extras', 'Admin Menu text', 'wp-liefermanager'),
            'name_admin_bar' => _x('Extra', 'Add New on Toolbar', 'wp-liefermanager'),
            'archives' => __('Extra Archives', 'wp-liefermanager'),
            'attributes' => __('Extra Attributes', 'wp-liefermanager'),
            'parent_item_colon' => __('Parent Extra:', 'wp-liefermanager'),
            'all_items' => __('All Extras', 'wp-liefermanager'),
            'add_new_item' => __('Add New Extra', 'wp-liefermanager'),
            'add_new' => __('Add New', 'wp-liefermanager'),
            'new_item' => __('New Extra', 'wp-liefermanager'),
            'edit_item' => __('Edit Extra', 'wp-liefermanager'),
            'update_item' => __('Update Extra', 'wp-liefermanager'),
            'view_item' => __('View Extra', 'wp-liefermanager'),
            'view_items' => __('View Extras', 'wp-liefermanager'),
            'search_items' => __('Search Extra', 'wp-liefermanager'),
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
}
