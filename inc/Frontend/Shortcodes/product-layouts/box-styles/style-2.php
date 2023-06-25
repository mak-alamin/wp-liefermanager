<?php

$style2 = '<h3>' . $post->post_title . '</h3>';

$style2 .= '<p class="price"><span class="triangle one"></span><span class="triangle two"></span>' . $product->get_price_html() . '</p>';

$style2 .= '<figure>' . $image . '</figure>';

$style2 .= '<p class="short-desc">';
$style2 .= $this->show_short_description($post->ID);
$style2 .= '</p>';

$style2 .= '<div class="layout-additives">';
$style2 .= $this->show_additives($post->ID);
$style2 .= '</div>';

$style2 .= '<div class="order-button">';

$style2 .= $this->generate_add_to_cart_button($post->ID);

$style2 .= '</div>'; //.order-button

return $style2;