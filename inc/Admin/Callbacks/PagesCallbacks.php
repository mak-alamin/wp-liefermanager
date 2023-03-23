<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Callbacks;

class PagesCallbacks
{
    public function dashboardPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/dashboard.php';
    }

    public function settingsPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/settings.php';
    }

    public function reservationsPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/reservations.php';
    }
    
    public function shortcodesPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/shortcodes.php';
    }
    
    public function toolsPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/tools.php';
    }
    
    public function licensePage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/license.php';
    }
   
    public function getHelpPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/get_help.php';
    }

}