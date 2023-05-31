<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin;

class Menus
{
	public $admin_pages = array();

	public $pages_callbacks;

	public function register()
	{
		$this->pages_callbacks = new Callbacks\PagesCallbacks();

		$this->setPages();

		if (!empty($this->admin_pages)) {
			add_action('admin_menu', array($this, 'addAdminPages'));

			// Add Post Types Pages
			add_action('admin_menu', array($this, 'addTableBookingAdminPage'), 11);

			add_action('admin_menu', array($this, 'addProductLayoutAdminPage'), 12);

			add_action('admin_menu', array($this, 'addFoodExtraAdminPage'), 13);
			
			add_action('admin_menu', array($this, 'addFoodBranchesAdminPage'), 14);
		}
	}

	private function setPages()
	{
		$this->admin_pages = require_once __DIR__ . '/config/pages.php';
	}

	public function addTableBookingAdminPage()
	{
		add_submenu_page('wp-liefermanager', _x('WP Liefermanager | Tischbestellung', 'wp-liefermanager'), _x('Tischbestellung', 'wp-liefermanager'), 'manage_options', 'edit.php?post_type=wp-liefer-tables');
	}

	public function addProductLayoutAdminPage()
	{
		add_submenu_page('wp-liefermanager', _x('WP Liefermanager | Produkt Layout', 'wp-liefermanager'), _x('Produkt Layout', 'wp-liefermanager'), 'manage_options', 'edit.php?post_type=wp-liefer-pr-layouts');
	}

	public function addFoodExtraAdminPage()
	{
		add_submenu_page('wp-liefermanager', _x('WP Liefermanager | Extras', 'wp-liefermanager'), _x('Extras', 'wp-liefermanager'), 'manage_options', 'edit.php?post_type=wp-liefer-extras');
	}
	
	public function addFoodBranchesAdminPage()
	{
		add_submenu_page(
            'wp-liefermanager',
            __('Filialen', 'wp-liefermanager'),
            __('Filialen', 'wp-liefermanager'),
            'manage_options',
            'edit-tags.php?taxonomy=wp_liefer_branches&post_type=product'
        );
	}

	public function addAdminPages()
	{
		foreach ($this->admin_pages as $page) {
			$parent_slug = $page['menu_slug'];
			$capability = $page['capability'];

			add_menu_page($page['page_title'], $page['menu_title'], $capability, $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);

			foreach ($page['subpages'] as $subpage) {
				add_submenu_page($parent_slug, $subpage['page_title'], $subpage['menu_title'], $capability, $subpage['menu_slug'], $subpage['callback']);
			}
		}
	}
}
