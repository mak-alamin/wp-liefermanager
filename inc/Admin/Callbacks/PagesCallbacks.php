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
}
