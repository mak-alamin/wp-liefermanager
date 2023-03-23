<?php

/**
* Plugin Name: WP Liefermanager
* Plugin URI: https: //example.com/plugins/the-basics/
* Description: Online Food Ordering System, Delivery/Pickup, Table Reservation with WooCommerce
* Version: 1.0.0
* Requires at least: 5.2
* Requires PHP: 7.2
* Author: Mak Alamin
* Author URI:
* License: GPL v2 or later
* License URI: https: //www.gnu.org/licenses/gpl-2.0.html
* Update URI: 
* Text Domain: wp-liefermanager
* Domain Path: /languages
*/


// If this file is called firectly, abort!!!
defined('ABSPATH') or die('Hey, Stay out of here. You are blocked!');

// Require once the Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}

if (file_exists(dirname(__FILE__) . '/functions.php')) {
	require_once dirname(__FILE__) . '/functions.php';
}

if (file_exists(dirname(__FILE__) . '/libs/cmb2.php')) {
	require_once dirname(__FILE__) . '/libs/cmb2.php';
}

/**
 * Define constants
 */
define('WP_LIEFERMANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_LIEFERMANAGER_PLUGIN_URL', plugins_url('/', __FILE__));
define('WP_LIEFERMANAGER_ASSETS', plugins_url('/assets', __FILE__));

define('WP_LIEFERMANAGER_ADMIN_DIR', WP_LIEFERMANAGER_PLUGIN_DIR . '/inc/Admin');
define('WP_LIEFERMANAGER_FRONTEND_DIR', WP_LIEFERMANAGER_PLUGIN_DIR . '/inc/Frontend');


/**
 * Initialize all the core classes of the plugin
 */
if (class_exists('Inc\\Init')) {
	Inc\Init::registerServices();
}