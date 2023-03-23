<?php

return array(
    // Admin Menu Pages
    array(
        'page_title' => 'WP Liefermanager',
        'menu_title' => 'WP Liefermanager',
        'capability' => 'manage_options',
        'menu_slug' => 'wp-liefermanager',
        'callback' => array($this->pages_callbacks, 'dashboardPage'),
        'icon_url' => 'dashicons-food',
        'position' => 2,
        'subpages' => array(
            array(
                'page_title' => 'WP Liefermanager | Dashboard',
                'menu_title' => 'Dashboard',
                'menu_slug' => 'wp-liefermanager',
                'callback' => array($this->pages_callbacks, 'dashboardPage'),
            ),
            array(
                'page_title' => 'WP Liefermanager | Settings',
                'menu_title' => 'Settings',
                'menu_slug' => 'wp-liefermanager-settings',
                'callback' => array($this->pages_callbacks, 'settingsPage'),
            ),
            array(
                'page_title' => 'WP Liefermanager | Reservations',
                'menu_title' => 'Reservations',
                'menu_slug' => 'wp-liefermanager-reservations',
                'callback' => array($this->pages_callbacks, 'reservationsPage'),
            ),
            array(
                'page_title' => 'WP Liefermanager | Shortcodes',
                'menu_title' => 'Shortcodes',
                'menu_slug' => 'wp-liefermanager-shortcodes',
                'callback' => array($this->pages_callbacks, 'shortcodesPage'),
            ),
            array(
                'page_title' => 'WP Liefermanager | Tools',
                'menu_title' => 'Tools',
                'menu_slug' => 'wp-liefermanager-tools',
                'callback' => array($this->pages_callbacks, 'toolsPage'),
            ),
            array(
                'page_title' => 'WP Liefermanager | License',
                'menu_title' => 'License',
                'menu_slug' => 'wp-liefermanager-license',
                'callback' => array($this->pages_callbacks, 'licensePage'),
            ),
            array(
                'page_title' => 'WP Liefermanager | Get Help',
                'menu_title' => 'Get Help',
                'menu_slug' => 'wp-liefermanager-get-help',
                'callback' => array($this->pages_callbacks, 'getHelpPage'),
            ),
        ),
    ),
);
