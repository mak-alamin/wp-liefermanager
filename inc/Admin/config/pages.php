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
            // array(
            //     'page_title' => _x('WP Liefermanager | Einstellungen', 'wp-liefermanager'),
            //     'menu_title' => _x('Einstellungen', 'wp-liefermanager'),
            //     'menu_slug' => 'wp-liefermanager-settings',
            //     'callback' => array($this->pages_callbacks, 'settingsPage'),
            // ),
            // array(
            //     'page_title' => _x('WP Liefermanager | Zusatzstoffe', 'wp-liefermanager'),
            //     'menu_title' => _x('Zusatzstoffe','wp-liefermanager'),
            //     'menu_slug' => 'wp-liefermanager-additives',
            //     'callback' => array($this->pages_callbacks, 'additivesPage'),
            // ),
            // array(
            //     'page_title' => _x('WP Liefermanager | Filialen','wp-liefermanager'),
            //     'menu_title' => _x('Filialen','wp-liefermanager'),
            //     'menu_slug' => 'wp-liefermanager-branches',
            //     'callback' => array($this->pages_callbacks, 'branchesPage'),
            // ),
            // array(
            //     'page_title' => _x('WP Liefermanager | Tischbestellung', 'wp-liefermanager'),
            //     'menu_title' => _x('Tischbestellung','wp-liefermanager'),
            //     'menu_slug' => 'wp-liefermanager-table-booking',
            //     'callback' => array($this->pages_callbacks, 'tableBookingPage'),
            // ),
            // array(
            //     'page_title' => _x('WP Liefermanager | Produkt Layout','wp-liefermanager'),
            //     'menu_title' => _x('Produkt Layout', 'wp-liefermanager'),
            //     'menu_slug' => 'wp-liefermanager-product-layout',
            //     'callback' => array($this->pages_callbacks, 'productLayoutPage'),
            // ),
            // array(
            //     'page_title' => _x('WP Liefermanager | Extras', 'wp-liefermanager'),
            //     'menu_title' => _x('Extras', 'wp-liefermanager'),
            //     'menu_slug' => 'wp-liefermanager-extras',
            //     'callback' => array($this->pages_callbacks, 'extrasPage'),
            // ),
        ),
    ),
);
