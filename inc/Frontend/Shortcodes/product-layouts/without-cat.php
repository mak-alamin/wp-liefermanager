<?php

$noCatHtml = '';

$layoutClasses = ($layoutType == 'list') ? $layoutType : $layoutType . " column-" . $gridColumn;

$products = $this->get_products(0, $branchId, $productSortBy);

$activeClass = ($isTabCategories && $key == 0) ? 'active' : '';

$term = get_term($catID);

$noCatHtml .= '<div class="products ' . $layoutClasses . '">';

if (empty($products)) {
    $noCatHtml .= '<p> Kein Produkt gefunden. </p>';
} else {
    foreach ($products as $key => $post) {
        $product = wc_get_product($post->ID);

        $title = get_the_title($post->ID);
        $link = get_the_permalink($post->ID);
        $image = get_the_post_thumbnail($post->ID, 'full');

        $noCatHtml .= '<div class="product">';

        $noCatHtml .= include __DIR__ . '/box-styles/style-' . $layoutStyle . '.php';

        $noCatHtml .= '</div>'; //.product
    }
}

$noCatHtml .= '</div>'; // .products

return $noCatHtml;
