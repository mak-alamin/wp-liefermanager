<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Shortcodes;

class ProductLayout
{
    public function register()
    {
        add_shortcode('wpliefermanager', array($this, 'add_product_layout_shortcode'));
    }

    public function add_product_layout_shortcode($atts)
    {
        $layoutId = isset($atts['id']) ? $atts['id'] : 0;

        $layoutType = carbon_get_post_meta($layoutId, 'wp_liefer_product_layout_type');

        $gridColumn = carbon_get_post_meta($layoutId, 'wp_liefer_layout_grid_column');

        $productCats = carbon_get_post_meta($layoutId, 'wp_liefer_layout_product_categories');

        $catTitleView = carbon_get_post_meta($layoutId, 'wp_liefer_layout_cat_title_view');

        if (!empty($productCats)) {
            echo '<div class="wpliefer-product-layout">';
            echo '<div class="cat-title-tabs">';
            echo '</div>';

            $layoutClasses = ($layoutType == 'list') ? $layoutType : $layoutType . " column-" . $gridColumn;

            echo '<div class="products ' . $layoutClasses . '">';

            foreach ($productCats as $key => $catID) {
                $products = $this->get_products_for_category($catID);

                foreach ($products as $key => $post) {
                    $product = wc_get_product($post->ID);

                    $title = get_the_title($post->ID);
                    $link = get_the_permalink($post->ID);
                    $image = get_the_post_thumbnail($post->ID, 'thumbnail');

                    echo '<div class="product">';
                    echo $image;
                    echo '<h3>' . $post->post_title . '</h3>';
                    echo '<p>' . $product->get_price_html() . '</p>';

                    echo $this->generate_add_to_cart_button($post->ID);

                    echo '</div>';
                }
            }

            echo '</div>';
            echo '</div>';
        }
    }

    public function get_products_for_category($category_id)
    {
        $args = array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => intval($category_id),
                ),
            ),
        );
        $products = get_posts($args);

        return $products;
    }

    function generate_add_to_cart_button($product_id)
    {
        $product = wc_get_product($product_id);

        if ($product->is_type('variable')) {
            $button_text = __('Select options', 'woocommerce');
            $button_url = get_permalink($product->get_id());
        } else {
            $button_text = __('Add to cart', 'woocommerce');
            $button_url = esc_url($product->add_to_cart_url());
        }

        return '<a href="' . $button_url . '" class="button add_to_cart">' . $button_text . '</a>';
    }
}
