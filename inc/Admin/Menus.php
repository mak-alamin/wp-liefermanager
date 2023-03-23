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
		if (!is_admin()) {
			return;
		}

		$this->pages_callbacks = new Callbacks\PagesCallbacks();

		$this->setPages();

		if (!empty($this->admin_pages)) {
			add_action('admin_menu', array($this, 'addAdminPages'));
		}
	}

	private function setPages()
	{
		$this->admin_pages = require_once __DIR__ . '/config/pages.php';
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