<?php

/**
 * @package  WP-Liefermanager
 */

namespace Inc;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function getServices()
    {
        return [
            Base\Assets::class,
            // Base\Ajax::class,
            Admin\Menus::class,
            Admin\Pages\Settings::class,
            Admin\Pages\Additives::class,
            Admin\Pages\Branches::class,
            Admin\Pages\TableBooking::class,
            Admin\Pages\ProductLayout::class,
            Admin\Pages\Extras::class,
            Admin\CPT\CPT::class,
            Admin\ProductMeta\ProductMeta::class,
            Frontend\Extras\Extras::class,
            Frontend\Additives\Additives::class,
            Frontend\Shortcodes\Shortcodes::class,
            Frontend\Shortcodes\ProductLayout::class,
            Frontend\Checkout\Tips::class,
            Frontend\Checkout\Delivery::class,
        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return
     */
    public static function registerServices()
    {
        foreach (self::getServices() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param  class $class    class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
