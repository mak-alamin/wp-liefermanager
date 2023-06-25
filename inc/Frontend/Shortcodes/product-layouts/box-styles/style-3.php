<?php

$style3 = '<div class="col-1">';
$style3 .= '<figure>' . $image . '</figure>';

$style3 .= '<div class="order-button">';

$style3 .= $this->generate_add_to_cart_button($post->ID);

$style3 .= '</div>'; //.order-button
$style3 .= '</div>'; //.col-1

$style3 .= '<div class="col-2">';
$style3 .= '<h3>' . $post->post_title . '</h3>';

$style3 .= '<p class="short-desc">';
$style3 .= $this->show_short_description($post->ID);
$style3 .= '</p>';

$style3 .= '<p>' . $product->get_price_html() . '</p>';

$style3 .= '<div class="layout-additives">';
$style3 .= $this->show_additives($post->ID);
$style3 .= '</div>';

$style3 .= '</div>'; //.col-2

return $style3;