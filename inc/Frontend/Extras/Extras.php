<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Extras;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Extras
{
    public function register()
    {
        if (is_admin()) {
            return;
        }

        add_action('woocommerce_before_add_to_cart_button', [$this, 'generate_extra_options']);
    }

    public function generate_extra_options()
    {
        echo "<div class='food_extras'>";
        echo "<h2>Zusätzliche Optionen: </h2>";

        $global_extra_ids = carbon_get_the_post_meta('global_extras');

        $global_extra_posts = array();

        if (!empty($global_extra_ids)) {
            foreach ($global_extra_ids as $key => $extra_id) {
                $global_extra_posts['global_' . $extra_id] = carbon_get_post_meta($extra_id, 'wp_liefer_global_extras');
            }
        }

        // echo "<h4>Global Extras: </h4>";

        if (!empty($global_extra_posts)) {
            foreach ($global_extra_posts as $global_extra_key => $global_extras) {
                if (!empty($global_extras)) {
                    foreach ($global_extras as $extra_key => $extra) {
                        if (isset($extra['extra_options']) && !empty($extra['extra_options'])) {

                            echo "<p class='option-title'><b>" . $extra['option_name'] . "</b></p>";

                            foreach ($extra['extra_options'] as $option_key => $option) {
                                $option_id = "global_extra_options[$global_extra_key][$extra_key][$option_key]";

                                echo "<p><input type='checkbox' id='$option_id' /><label for='$option_id' class='ml-1'>" . $option['option_name'] . "</labe></p>";
                            }
                        }
                    }
                }
            }
        }


        // echo "<hr>";
        // echo "<h4>Product Extras: </h4>";

        $product_extras = carbon_get_the_post_meta('wp_liefer_product_extras');

        if (!empty($product_extras)) {
            foreach ($product_extras as $extra_key => $extra) {
                if (isset($extra['extra_options']) && !empty($extra['extra_options'])) {

                    echo "<p class='option-title'><b>" . $extra['option_name'] . "</b></p>";

                    foreach ($extra['extra_options'] as $opt_key => $option) {
                        $option_id = "product_extra_options[$extra_key][$opt_key]";

                        echo "<p><input type='checkbox' id='$option_id' /><label for='$option_id' class='ml-1'>" . $option['option_name'] . "</labe></p>";
                    }
                }
            }
        }

        echo "</div>";
    }
}
