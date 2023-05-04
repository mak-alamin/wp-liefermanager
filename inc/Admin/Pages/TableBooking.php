<?php

/**
 * @package  WP_LIEFERMANAGER
 */

namespace Inc\Admin\Pages;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class TableBooking
{
    public function register()
    {
        add_action('carbon_fields_register_fields', [$this, 'generate_options_with_carbon_fields']);
    }

    function generate_options_with_carbon_fields()
    {
        Container::make('post_meta', __('Tischinformationen', 'wp-liefermanager'))
            ->where('post_type', '=', 'wp-liefer-tables')
            ->add_fields(array(
                Field::make('text', 'wp_liefer_table_id', 'Tisch ID'),
                Field::make('text', 'wp_liefer_table_product_url', 'Tischprodukt URL'),
            ));
    }
}
