<?php

$withCatHtml = '';

if ($isTabCategories) {
    $withCatHtml .= '<div class="tab-wrapper cat-title-tabs ' . $catTitleView . '">';

    $tabDirection = ($catTitleView == 'top_tabs') ? 'horizontal' : 'vertical';

    $withCatHtml .= '<ul class="tab-menu ' . $tabDirection . '">';

    if (!empty($productCats)) {
        foreach ($productCats as $key => $catID) {
            $term = get_term($catID);

            $activeClass = ($key == 0) ? 'active' : '';

            $withCatHtml .= '<li class="' . $activeClass . '"><a href="#category-' . $catID . '">' . $term->name . '</a></li>';
        }
    }

    $withCatHtml .= '</ul>';
}

if ($isTabCategories) {
    $withCatHtml .= '<div class="tab-content">';
}

$layoutClasses = ($layoutType == 'list') ? $layoutType : $layoutType . " column-" . $gridColumn;

if (!empty($productCats)) {
    foreach ($productCats as $key => $catID) {
        $products = $this->get_products($catID, $branchId, $productSortBy);

        $activeClass = ($isTabCategories && $key == 0) ? 'active' : '';

        if ($isTabCategories) {
            $withCatHtml .= ' <div id="category-' . $catID . '" class="tab-pane ' . $activeClass . '">';
        }

        $term = get_term($catID);

        $withCatHtml .= ($catTitleView == 'cat_titles') ? '<h4 class="cat-title">' . $term->name . '</h4>' : '';

        $withCatHtml .= '<div class="products ' . $layoutClasses . '">';

        if (empty($products)) {
            $withCatHtml .= '<p> Kein Produkt gefunden. </p>';
        } else {
            foreach ($products as $key => $post) {
                $product = wc_get_product($post->ID);

                $title = get_the_title($post->ID);
                $link = get_the_permalink($post->ID);
                $image = get_the_post_thumbnail($post->ID, 'full');

                $withCatHtml .= '<div class="product">';

                $withCatHtml .= include __DIR__ . '/box-styles/style-' . $layoutStyle . '.php';

                $withCatHtml .= '</div>'; //.product
            }
        }

        $withCatHtml .= '</div>'; // .products

        if ($isTabCategories) {
            $withCatHtml .= '</div>'; // .tab-pane
        }
    }
}

if ($isTabCategories) {
    $withCatHtml .= '</div>'; // .tab-content

    $withCatHtml .= '</div>'; // .tab-wrapper
}

return $withCatHtml;
