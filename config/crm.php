<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Configure the default pagination behavior for CRM modules.
    |
    */

    'pagination' => [
        'default_per_page' => 20,
        'per_page_options' => [20, 50, 100, 500, 1000],
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Module Settings
    |--------------------------------------------------------------------------
    */

    'lead' => [
        // Whitelist of columns allowed for sorting (security: prevent SQL injection)
        'sortable_columns' => ['name', 'phone', 'email', 'status', 'created_at', 'updated_at'],
    ],

];
