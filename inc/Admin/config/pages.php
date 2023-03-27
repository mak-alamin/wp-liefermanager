<?php

return array(
    // Admin Menu Pages
    array(
        'page_title' => _x('WP Liefermanager', 'wp-liefermanager'),
        'menu_title' => _x('WP Liefermanager', 'wp-liefermanager'),
        'capability' => 'manage_options',
        'menu_slug' => 'wp-liefermanager',
        'callback' => array($this->pages_callbacks, 'dashboardPage'),
        'icon_url' => 'dashicons-food',
        'position' => 2,
        'subpages' => array(
            array(
                'page_title' => _x('WP Liefermanager | Dashboard', 'wp-liefermanager'),
                'menu_title' => _x('Dashboard', 'wp-liefermanager'),
                'menu_slug' => 'wp-liefermanager',
                'callback' => array($this->pages_callbacks, 'dashboardPage'),
            ),
        ),
    ),
);
