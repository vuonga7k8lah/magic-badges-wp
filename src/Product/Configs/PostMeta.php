<?php

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

return [
    'manual_general_settings_section' => [
        'id'           => 'manual_general_settings_section',
        'title'        => esc_html__('Manual Settings', MYSHOPKIT_MB_WP_REST_NAMESPACE),
        'object_types' => [AutoPrefix::namePrefix('manual')],
        'fields'       => [
            'config'     => [
                'name'       => esc_html__('Manual Configuration', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                'save_field' => true,
                'id'         => 'config',
                'type'       => 'textarea'
            ],
            'badge_id'   => [
                'name'       => esc_html__('Badge ID', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                'save_field' => true,
                'id'         => 'badge_id',
                'type'       => 'text'
            ],
            'product_id' => [
                'name'       => esc_html__('Product ID', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                'save_field' => true,
                'id'         => 'product_id',
                'type'       => 'text'
            ]
        ]
    ]
];
