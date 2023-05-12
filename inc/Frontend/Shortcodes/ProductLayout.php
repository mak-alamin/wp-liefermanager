<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Frontend\Shortcodes;

class ProductLayout extends \Inc\Base\Common
{
    public function register()
    {
        add_shortcode('wpliefermanager', array($this, 'add_product_layout_shortcode'));

        add_action('wp_ajax_wpliefer_get_product_html', array($this, 'get_product_html'));
        add_action('wp_ajax_nopriv_wpliefer_get_product_html', array($this, 'get_product_html'));
    }

    public function get_product_html()
    {
        $product_url = $_REQUEST['productUrl'];

        $response = wp_remote_get($product_url); // make a remote GET request to the product URL

        $product_html = wp_remote_retrieve_body($response); // retrieve the HTML content from the response

        wp_send_json($product_html);
    }

    public function add_product_layout_shortcode($atts)
    {
        $layoutId = isset($atts['id']) ? $atts['id'] : 0;

        $productCats = carbon_get_post_meta($layoutId, 'wp_liefer_layout_product_categories');

        if (empty($productCats)) {
            return;
        }

        $layoutType = carbon_get_post_meta($layoutId, 'wp_liefer_product_layout_type');

        $gridColumn = carbon_get_post_meta($layoutId, 'wp_liefer_layout_grid_column');

        $catTitleView = carbon_get_post_meta($layoutId, 'wp_liefer_layout_cat_title_view');

        $isTabCategories = $catTitleView == 'top_tabs' || $catTitleView == 'left_tabs';

        echo '<div class="wpliefer-product-layout">';

        if ($isTabCategories) {
            echo '<div class="tab-wrapper cat-title-tabs ' . $catTitleView . '">';

            $tabDirection = ($catTitleView == 'top_tabs') ? 'horizontal' : 'vertical';

            echo '<ul class="tab-menu ' . $tabDirection . '">';
            foreach ($productCats as $key => $catID) {
                $term = get_term($catID);

                $activeClass = ($key == 0) ? 'active' : '';

                echo '<li class="' . $activeClass . '"><a href="#category-' . $catID . '">' . $term->name . '</a></li>';
            }
            echo '</ul>';
        }

        if ($isTabCategories) {
            echo '<div class="tab-content">';
        }

        $layoutClasses = ($layoutType == 'list') ? $layoutType : $layoutType . " column-" . $gridColumn;

        foreach ($productCats as $key => $catID) {
            $products = $this->get_products_for_category($catID);

            $activeClass = ($isTabCategories && $key == 0) ? 'active' : '';

            echo ' <div id="category-' . $catID . '" class="tab-pane ' . $activeClass . '">';

            $term = get_term($catID);

            echo ($catTitleView == 'cat_titles') ? '<h4 class="cat-title">' . $term->name . '</h4>' : '';

            echo '<div class="products ' . $layoutClasses . '">';

            foreach ($products as $key => $post) {
                $product = wc_get_product($post->ID);

                $title = get_the_title($post->ID);
                $link = get_the_permalink($post->ID);
                $image = get_the_post_thumbnail($post->ID, 'thumbnail');

                echo '<div class="product">';
                echo '<figure>' . $image . '</figure>';

                echo '<div class="layout-additives">';

                echo '<h3>' . $post->post_title . '</h3>';

                echo '<p class="short-desc">';
                $this->show_short_description($post->ID);
                echo '</p>';

                $this->show_additives($post->ID);
                echo '</div>';

                echo '<div class="order-button">';
                echo '<p>' . $product->get_price_html() . '</p>';

                echo $this->generate_add_to_cart_button($post->ID);

                echo '</div>'; //product-info
                echo '</div>'; //product
            }
            echo '</div>'; // .products
            echo '</div>'; // .tab-pane
        }

        if ($isTabCategories) {
            echo '</div>'; // .tab-content

            echo '</div>'; // .tab-wrapper
        }

        echo '</div>'; // .wpliefer-product-layout
    }
}
