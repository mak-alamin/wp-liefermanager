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

        // add_action('wp_ajax_wpliefer_get_product_html', array($this, 'get_product_html'));
        // add_action('wp_ajax_nopriv_wpliefer_get_product_html', array($this, 'get_product_html'));
    }

    public function get_product_html()
    {
        $product_url = $_REQUEST['productUrl'];

        $response = wp_remote_get($product_url);

        $product_html = wp_remote_retrieve_body($response);

        wp_send_json($product_html);
    }

    public function add_product_layout_shortcode($atts)
    {
        wp_enqueue_style('slick', WP_LIEFERMANAGER_ASSETS . '/frontend/libs/slick/slick.css', null, false, 'all');

        wp_enqueue_style('slick-theme', WP_LIEFERMANAGER_ASSETS . '/frontend/libs/slick/slick-theme.css', null, false, 'all');

        wp_enqueue_script('slick', WP_LIEFERMANAGER_ASSETS . '/frontend/libs/slick/slick.js', 'jquery', false, true);

        $layoutId = isset($atts['layout_id']) ? $atts['layout_id'] : 0;

        $branchId = isset($atts['branch_id']) ? $atts['branch_id'] : 0;

        $branchId = 0;

        if ($this->get_branchId()) {
            $branchId = $this->get_branchId();
        }

        $productCats = carbon_get_post_meta($layoutId, 'wp_liefer_layout_product_categories');

        $productSortBy = carbon_get_post_meta($layoutId, 'wp_liefer_product_sorting');

        $tableId = (isset($_GET['table_id'])) ? $_GET['table_id'] : 0;

        if ($tableId) {
            WC()->session->set('table_id', $tableId);
        }

        $layoutType = carbon_get_post_meta($layoutId, 'wp_liefer_product_layout_type');

        $gridColumn = carbon_get_post_meta($layoutId, 'wp_liefer_layout_grid_column');

        $layoutStyle = carbon_get_post_meta($layoutId, 'wp_liefer_layout_style');

        $catTitleView = carbon_get_post_meta($layoutId, 'wp_liefer_layout_cat_title_view');

        $isTabCategories = $catTitleView == 'top_tabs' || $catTitleView == 'left_tabs';

        $html = '<div class="wpliefer-product-layout style-' . $layoutStyle . '">';

        if ($catTitleView == 'no_titles') {
            $html .= require_once __DIR__ . '/product-layouts/without-cat.php';
        } else {
            $html .= require_once __DIR__ . '/product-layouts/with-cat.php';
        }

        $html .= '</div>'; // .wpliefer-product-layout

        return $html;
    }
}
