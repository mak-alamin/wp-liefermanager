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
        add_action('woocommerce_before_add_to_cart_button', [$this, 'generate_extra_options']);

        add_filter('woocommerce_add_cart_item_data', [$this, 'add_extra_data_to_cart_item'], 10, 2);

        add_filter('woocommerce_get_item_data', [$this, 'display_extra_data_on_cart'], 10, 2);

        add_action('woocommerce_before_calculate_totals', [$this, 'add_extra_prices']);

        // Show variation extras
        add_action('wp_ajax_wp_liefer_show_variation_extras', [$this, 'wp_liefer_show_variation_extras']);

        add_action('wp_ajax_nopriv_wp_liefer_show_variation_extras', [$this, 'wp_liefer_show_variation_extras']);

        // Show Total Price
        add_action('woocommerce_after_add_to_cart_quantity', [$this, 'show_total_price']);
    }

    public function show_total_price()
    {
        global $product;

        $price = $product->get_price();

        echo '<h2>Gesamt: <span class="wp-liefer-total-price">' . $price . '</span>' . get_woocommerce_currency_symbol() . '</h2>';
    }

    function wp_liefer_show_variation_extras()
    {
        $global_extras = get_posts(array(
            'post_type'        => 'wp-liefer-extras',
            'posts_per_page'   => -1,
        ));

        $selected_extras = array();

        $product_id = intval($_REQUEST['productId']);
        $size_attr = strval($_REQUEST['sizeAttr']);

        $match_attributes =  array(
            "attribute_pa_pizza-groessen" => $size_attr,
        );

        $data_store   = \WC_Data_Store::load('product');

        $variation_id = $data_store->find_matching_product_variation(
            new \WC_Product($product_id),
            $match_attributes
        );

        $price = wc_get_product($variation_id)->get_price();

        if (!empty($global_extras)) {
            foreach ($global_extras as $key => $extra) {
                $useInVariation = carbon_get_post_meta($extra->ID, 'wp_liefer_use_in_variations');

                $existsInVariation = get_post_meta($variation_id,  'food_variation_extra_' . $variation_id . '_' . $extra->ID);

                if ($useInVariation && $existsInVariation) {
                    $selected_extras[$extra->ID] = carbon_get_post_meta($extra->ID, 'wp_liefer_global_extras');
                }
            }
        }

        $extra_html = $this->generate_global_extras($selected_extras);


        wp_send_json(array(
            'price' => $price,
            'extra_html' => $extra_html,
            'extras' => $selected_extras
        ));
    }

    public function add_extra_prices($cart_object)
    {
        foreach ($cart_object->get_cart() as $hash => $value) {
            $price = $value['data']->get_price();

            $new_price = $price;

            if (isset($value['global_extras']) && !empty(isset($value['global_extras']))) {
                $extra_price = 0;

                foreach ($value['global_extras'] as $key => $option) {
                    $extra_price += intval($option['quantity']) * floatval($option['option_price']);
                }

                $new_price += $extra_price;
            }

            if (isset($value['product_extras']) && !empty(isset($value['product_extras']))) {
                $extra_price = 0;

                foreach ($value['product_extras'] as $key => $option) {
                    $extra_price += intval($option['quantity']) * floatval($option['option_price']);
                }

                $new_price += $extra_price;
            }

            $value['data']->set_price($new_price);
        }
    }

    // Add extra data to cart item when add to cart
    public function add_extra_data_to_cart_item($cart_item_data, $product_id)
    {
        $choosen_global_options = isset($_REQUEST['global_extra_options']) ? $_REQUEST['global_extra_options'] : null;

        $choosen_product_options = isset($_REQUEST['product_extra_options']) ? $_REQUEST['product_extra_options'] : null;

        if (empty($choosen_global_options) && empty($choosen_product_options)) {
            return $cart_item_data;
        }

        if (!empty($choosen_global_options)) {
            foreach ($choosen_global_options as $key => $options) {
                if (is_array($options)) {
                    foreach ($options as $opt_key => $option) {
                        $option = (array) json_decode(stripslashes($option));

                        $option['option_price'] = str_replace('.', '', $option['option_price']);
                        $option['option_price'] = str_replace(',', '.', $option['option_price']);

                        $cart_item_data['global_extras'][] = $option;
                    }
                } else {
                    $options = (array) json_decode(stripslashes($options));

                    $options['option_price'] = str_replace('.', '', $options['option_price']);
                    $options['option_price'] = str_replace(',', '.', $options['option_price']);

                    $cart_item_data['global_extras'][] = $options;
                }
            }
        }

        if (!empty($choosen_product_options)) {
            foreach ($choosen_product_options as $key => $options) {
                if (is_array($options)) {
                    foreach ($options as $opt_key => $option) {
                        $option =  (array) json_decode(stripslashes($option));

                        $option['option_price'] = str_replace('.', '', $option['option_price']);
                        $option['option_price'] = str_replace(',', '.', $option['option_price']);

                        $cart_item_data['product_extras'][] = $option;
                    }
                } else {
                    $options = (array) json_decode(stripslashes($options));

                    $options['option_price'] = str_replace('.', '', $options['option_price']);
                    $options['option_price'] = str_replace(',', '.', $options['option_price']);

                    $cart_item_data['product_extras'][] = $options;
                }
            }
        }     

        return $cart_item_data;
    }

    // Display extra data on cart
    public function display_extra_data_on_cart($item_data, $cart_item)
    {
        if (isset($cart_item['global_extras']) && !empty($cart_item['global_extras'])) {
            foreach ($cart_item['global_extras'] as $key => $option) {
                if (isset($option['option_name'])) {
                    $item_data[] = array(
                        // $option['quantity']  . ' x ' .
                        'key'   => $option['option_name'],
                        'value' => ' (' . intval($option['quantity']) * floatval($option['option_price']) . get_woocommerce_currency_symbol() . ')',
                    );
                }
            }
        }

        if (isset($cart_item['product_extras']) && !empty($cart_item['product_extras'])) {
            foreach ($cart_item['product_extras'] as $key => $option) {
                if (isset($option['option_name'])) {
                    $item_data[] = array(
                        'key'   => $option['option_name'],
                        'value' => ' (' . intval($option['quantity']) * floatval($option['option_price']) . get_woocommerce_currency_symbol() . ')',
                    );
                }
            }
        }

        return $item_data;
    }

    public function generate_extra_options()
    {
        echo "<div class='food_extras'>";

        $global_extra_ids = carbon_get_the_post_meta('global_extras');

        $global_extra_posts = array();

        if (!empty($global_extra_ids)) {
            foreach ($global_extra_ids as $key => $extra_id) {
                $global_extra_posts[$extra_id] = carbon_get_post_meta($extra_id, 'wp_liefer_global_extras');
            }
        }

        $product_extras = carbon_get_the_post_meta('wp_liefer_product_extras');

        if (!empty($global_extra_posts) || !empty($product_extras)) {
            echo "<h2>Zusätzliche Optionen: </h2>";
        }

        // echo "<h4>Global Extras: </h4>";
        echo $this->generate_global_extras($global_extra_posts);


        // echo "<hr>";
        // echo "<h4>Product Extras: </h4>";

        if (!empty($product_extras)) {
            $serial = 0;
            foreach ($product_extras as $extra_key => $extra) {
                if (isset($extra['extra_options']) && !empty($extra['extra_options'])) {

                    echo "<p class='option-title'><b>" . $extra['option_name'] . "</b></p>";

                    // Input Type: Select
                    if ($extra['option_type'] == 'select') {
                        echo "<p class='wp_liefer_extra_option'>";

                        echo "<select name='product_extra_options[$serial]' class='extra_option'>";

                        echo "<option selected>Choose Option</option>";

                        foreach ($extra['extra_options'] as $option_key => $option) {
                            $price_type = $option['price_type'];

                            $option['quantity'] = 1;

                            echo "<option value='" . json_encode($option) . "'>" . $option['option_name'] . ' (' . $option['option_price'] . get_woocommerce_currency_symbol() . ")</option>";
                        }

                        echo "</select>";

                        // echo ($price_type == 'quantity') ? "<input type='number' value='1' min='1' class='change_quantity' />" : "";

                        echo "</p>";
                    }

                    $serial++;

                    // Input Type: Radio
                    if ($extra['option_type'] == 'radio') {
                        $option_name = "product_extra_options[$serial]";

                        foreach ($extra['extra_options'] as $option_key => $option) {
                            $option_id = "product_extra_options_" . $global_extra_key . '_' . $extra_key . '_' . $option_key;

                            $price_type = $option['price_type'];

                            $option['quantity'] = 1;

                            echo "<p class='wp_liefer_extra_option'>";

                            echo "<input type='radio' name='$option_name' id='$option_id' value='" . json_encode($option) . "' class='extra_option' /><label for='$option_id' class='ml-1'>" . $option['option_name'] . ' (' . $option['option_price'] . get_woocommerce_currency_symbol() . ")</label>";

                            // echo ($price_type == 'quantity') ? "<input type='number' value='1' min='1' class='change_quantity' />" : "";

                            echo "</p>";
                        }
                    }

                    $serial++;

                    // Input Type: Checkbox
                    if ($extra['option_type'] == 'checkbox') {
                        foreach ($extra['extra_options'] as $option_key => $option) {

                            $option_id = "product_extra_options_" . $global_extra_key . '_' . $extra_key . '_' . $option_key;

                            $option_name = "product_extra_options[$serial][$option_key]";

                            $price_type = $option['price_type'];

                            $option['quantity'] = 1;

                            echo "<p class='wp_liefer_extra_option'>";

                            echo "<input type='checkbox' name='$option_name' id='$option_id' value='" . json_encode($option) . "' class='extra_option' /><label for='$option_id' class='ml-1'>" . $option['option_name'] . ' (' . $option['option_price'] . get_woocommerce_currency_symbol() . ")</label>";

                            // echo ($price_type == 'quantity') ? "<input type='number' value='1' min='1' class='change_quantity' />" : "";

                            echo "</p>";
                        }
                    }
                }
                $serial++;
            }
        }

        echo "</div>";
    }

    public function generate_global_extras($global_extra_posts)
    {
        $extra_html = '';

        if (!empty($global_extra_posts)) {
            $serial = 0;
            foreach ($global_extra_posts as $global_extra_key => $global_extras) {
                if (!empty($global_extras)) {        
                    foreach ($global_extras as $extra_key => $extra) {
                        if (isset($extra['extra_options']) && !empty($extra['extra_options'])) {
                             
                            $extra_html .= '<div class="wp_liefer_extra_options" data-min="'.$extra['option_min'].'" data-max="'.$extra['option_max'].'">';

                            $selection_error = empty($extra['option_min']) ? '' : '<span class="error-text">(Mindestauswahl ' . $extra['option_min'] . ')</span>';

                            $extra_html .= "<p class='option-title'><b>" . $extra['option_name'] . "</b> ". $selection_error ."</p>";

                            $price_type = 'quantity';

                            // Input Type: Select
                            if ($extra['option_type'] == 'select') {
                                $extra_html .= "<p class='wp_liefer_extra_option'>";
                                $extra_html .= "<select name='global_extra_options[$serial]' class='extra_option'>";

                                $extra_html .= "<option selected>Choose Option</option>";

                                foreach ($extra['extra_options'] as $option_key => $option) {
                                    $price_type = $option['price_type'];

                                    $option['quantity'] = 1;

                                    $extra_html .= "<option value='" . json_encode($option) . "'>" . $option['option_name'] . ' (' . $option['option_price'] . get_woocommerce_currency_symbol() . ")</option>";
                                }

                                $extra_html .= "</select>";

                                // if ($price_type == 'quantity') {
                                //     $extra_html .= "<input type='number' value='1' min='1' class='change_quantity' />";
                                // }

                                $extra_html .= "</p>";
                            }

                            $serial++;

                            // Input Type: Radio
                            if ($extra['option_type'] == 'radio') {
                                $option_name = "global_extra_options[$serial]";

                                foreach ($extra['extra_options'] as $option_key => $option) {
                                    $option_id = "global_extra_options_" . $global_extra_key . '_' . $extra_key . '_' . $option_key;

                                    $price_type = $option['price_type'];

                                    $option['quantity'] = 1;

                                    $extra_html .= "<p class='wp_liefer_extra_option'>";

                                    $extra_html .= "<input type='radio' name='$option_name' id='$option_id' value='" . json_encode($option) . "' class='extra_option' /><label for='$option_id' class='ml-1'>" . $option['option_name'] . ' (' . $option['option_price'] . get_woocommerce_currency_symbol() . ")</label>";

                                    // $extra_html .= ($price_type == 'quantity') ? "<input type='number' value='1' min='1' class='change_quantity' />" : "";

                                    $extra_html .= "</p>";
                                }
                            }

                            $serial++;

                            // Input Type: Checkbox
                            if ($extra['option_type'] == 'checkbox') {
                                foreach ($extra['extra_options'] as $option_key => $option) {

                                    $option_id = "global_extra_options_" . $global_extra_key . '_' . $extra_key . '_' . $option_key;

                                    $option_name = "global_extra_options[$serial][$option_key]";

                                    $price_type = $option['price_type'];

                                    $option['quantity'] = 1;

                                    $extra_html .= "<p class='wp_liefer_extra_option'>";

                                    $extra_html .= "<input type='checkbox' name='$option_name' id='$option_id' value='" . json_encode($option) . "' class='extra_option' /><label for='$option_id' class='ml-1'>" . $option['option_name'] . ' (' . $option['option_price'] . get_woocommerce_currency_symbol() . ")</label>";

                                    // $extra_html .= ($price_type == 'quantity') ? "<input type='number' value='1' min='1' class='change_quantity' />" : "";

                                    $extra_html .= "</p>";
                                }
                            }

                            $extra_html .= '</div>';
                        }

                        $serial++;
                    }
                }
                $serial++;
            }
        }

        return $extra_html;
    }
}
