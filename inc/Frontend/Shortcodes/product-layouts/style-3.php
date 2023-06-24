<?php

$html .= '<div class="wpliefer-product-layout style-3">';

if ($isTabCategories) {
    $html .= '<div class="tab-wrapper cat-title-tabs ' . $catTitleView . '">';

    $tabDirection = ($catTitleView == 'top_tabs') ? 'horizontal' : 'vertical';

    $html .= '<ul class="tab-menu ' . $tabDirection . '">';

    if (!empty($productCats)) {
        foreach ($productCats as $key => $catID) {
            $term = get_term($catID);

            $activeClass = ($key == 0) ? 'active' : '';

            $html .= '<li class="' . $activeClass . '"><a href="#category-' . $catID . '">' . $term->name . '</a></li>';
        }
    }

    $html .= '</ul>';
}

if ($isTabCategories) {
    $html .= '<div class="tab-content">';
}

$layoutClasses = ($layoutType == 'list') ? $layoutType : $layoutType . " column-" . $gridColumn;

if ($catTitleView == 'no_titles') {
    $html .= '<div class="products ' . $layoutClasses . '">';
}

if (!empty($productCats)) {
    foreach ($productCats as $key => $catID) {
        $products = $this->get_products($catID, $branchId);

        $activeClass = ($isTabCategories && $key == 0) ? 'active' : '';

        if ($isTabCategories) {
            $html .= ' <div id="category-' . $catID . '" class="tab-pane ' . $activeClass . '">';
        }

        $term = get_term($catID);

        $html .= ($catTitleView == 'cat_titles') ? '<h4 class="cat-title">' . $term->name . '</h4>' : '';

        if ($catTitleView != 'no_titles') {
            $html .= '<div class="products ' . $layoutClasses . '">';
        }

        if (empty($products)) {
            $html .= '<p> Kein Produkt gefunden. </p>';
        } else {
            foreach ($products as $key => $post) {
                $product = wc_get_product($post->ID);

                $title = get_the_title($post->ID);
                $link = get_the_permalink($post->ID);
                $image = get_the_post_thumbnail($post->ID, 'thumbnail');

                $html .= '<div class="product">';
                $html .= '<figure>' . $image . '</figure>';

                $html .= '<div class="layout-additives">';

                $html .= '<h3>' . $post->post_title . '</h3>';

                $html .= '<p class="short-desc">';
                $html .= $this->show_short_description($post->ID);
                $html .= '</p>';

                $html .= $this->show_additives($post->ID);
                $html .= '</div>';

                $html .= '<div class="order-button">';
                $html .= '<p>' . $product->get_price_html() . '</p>';

                $html .= $this->generate_add_to_cart_button($post->ID);

                $html .= '</div>'; //product-info
                $html .= '</div>'; //product
            }
        }

        if ($catTitleView != 'no_titles') {
            $html .= '</div>'; // .products
        }

        if ($isTabCategories) {
            $html .= '</div>'; // .tab-pane
        }
    }
}

if ($catTitleView == 'no_titles') {
    $html .= '</div>'; // .products
}

if ($isTabCategories) {
    $html .= '</div>'; // .tab-content

    $html .= '</div>'; // .tab-wrapper
}

$html .= '</div>'; // .wpliefer-product-layout

return $html;
