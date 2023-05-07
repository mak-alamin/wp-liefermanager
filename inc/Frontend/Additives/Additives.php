<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Additives;

class Additives extends \Inc\Base\Common
{
    public function register()
    {
        add_action('woocommerce_product_meta_end', [$this, 'show_product_additives']);
    }

    public function show_product_additives()
    {
        $this->show_additives(get_the_ID());
    }
}
