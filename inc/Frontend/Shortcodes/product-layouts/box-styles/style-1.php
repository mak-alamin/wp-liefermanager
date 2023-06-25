<?php

$style1 = '<figure>' . $image . '</figure>';

$style1 .= '<div class="layout-additives">';

$style1 .= '<h3>' . $post->post_title . '</h3>';

$style1 .= '<p class="short-desc">';
$style1 .= $this->show_short_description($post->ID);
$style1 .= '</p>';

$style1 .= $this->show_additives($post->ID);
$style1 .= '</div>';

$style1 .= '<div class="order-button">';
$style1 .= '<p>' . $product->get_price_html() . '</p>';

$style1 .= $this->generate_add_to_cart_button($post->ID);

$style1 .= '</div>'; //.order-button

return $style1;