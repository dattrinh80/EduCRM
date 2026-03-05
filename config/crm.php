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

        // Số trang hiển thị trước và sau trang hiện tại (ví dụ: 2 → hiện trang -2, -1, current, +1, +2)
        'page_window' => 2,

        // Nếu tổng số trang <= giá trị này thì hiển thị tất cả, không cần dấu "..."
        'max_pages_before_truncation' => 7,
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
