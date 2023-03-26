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

    public function additivesPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/additives.php';
    }
    
    public function branchesPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/branches.php';
    }
    
    public function tableBookingPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/table-booking.php';
    }
    
    public function productLayoutPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/product-layout.php';
    }
   
    public function extrasPage()
    {
        require_once WP_LIEFERMANAGER_ADMIN_DIR . '/templates/pages/extras.php';
    }

}