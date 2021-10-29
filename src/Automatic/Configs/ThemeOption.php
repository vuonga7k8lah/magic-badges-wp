<?php


$prefixNewArrival = 'default-new-arrival';
$prefixOutOfStock = 'default-out-of-stock';
$prefixOnSale = 'default-on-sale';
return [
    'new-arrival'  => [
        'title'            => esc_html__('Default New Arrival Settings', MYSHOPKIT_MB_REST_NAMESPACE),
        'id'               => 'default-new-arrival-settings',
        'subsection'       => true,
        'customizer_width' => '450px',
        'fields'           => [
            [
                'id'    => $prefixNewArrival . 'badgeID',
                'type'  => 'text',
                'title' => esc_html__('Badge ID', MYSHOPKIT_MB_REST_NAMESPACE),
            ],
            [
                'id'      => $prefixNewArrival . 'title',
                'type'    => 'text',
                'default' => esc_html__('New Arrival', MYSHOPKIT_MB_REST_NAMESPACE),
                'title'   => esc_html__('Title', MYSHOPKIT_MB_REST_NAMESPACE),
            ],
            [
                'id'      => $prefixNewArrival . 'description',
                'type'    => 'textarea',
                'default' => esc_html__('Show badge on products that are added in last 7 days',
                    MYSHOPKIT_MB_REST_NAMESPACE),
                'title'   => esc_html__('Description', MYSHOPKIT_MB_REST_NAMESPACE),
            ]
        ]
    ],
    'out-of-stock' => [
        'title'            => esc_html__('Default Out Of Stock Settings', MYSHOPKIT_MB_REST_NAMESPACE),
        'id'               => 'default-out-of-stock-settings',
        'subsection'       => true,
        'customizer_width' => '450px',
        'fields'           => [
            [
                'id'    => $prefixOutOfStock . 'badgeID',
                'type'  => 'text',
                'title' => esc_html__('Badge ID', MYSHOPKIT_MB_REST_NAMESPACE),
            ],
            [
                'id'      => $prefixOutOfStock . 'title',
                'type'    => 'text',
                'default' => esc_html__('Out Of Stock', MYSHOPKIT_MB_REST_NAMESPACE),
                'title'   => esc_html__('Title', MYSHOPKIT_MB_REST_NAMESPACE),
            ],
            [
                'id'      => $prefixOutOfStock . 'description',
                'type'    => 'textarea',
                'default' => esc_html__('Show badge when stock drops to zero',
                    MYSHOPKIT_MB_REST_NAMESPACE),
                'title'   => esc_html__('Description', MYSHOPKIT_MB_REST_NAMESPACE),
            ]
        ]
    ],
    'on-sale'      => [
        'title'            => esc_html__('Default On Sale Settings', MYSHOPKIT_MB_REST_NAMESPACE),
        'id'               => 'default-on-sale-settings',
        'subsection'       => true,
        'customizer_width' => '450px',
        'fields'           => [
            [
                'id'    => $prefixOnSale . 'badgeID',
                'type'  => 'text',
                'title' => esc_html__('Badge ID', MYSHOPKIT_MB_REST_NAMESPACE),
            ],
            [
                'id'      => $prefixOnSale . 'title',
                'type'    => 'text',
                'default' => esc_html__('On Sale', MYSHOPKIT_MB_REST_NAMESPACE),
                'title'   => esc_html__('Title', MYSHOPKIT_MB_REST_NAMESPACE),
            ],
            [
                'id'      => $prefixOnSale . 'description',
                'type'    => 'textarea',
                'default' => esc_html__('Show badge on products with discount crated using price rules',
                    MYSHOPKIT_MB_REST_NAMESPACE),
                'title'   => esc_html__('Description', MYSHOPKIT_MB_REST_NAMESPACE),
            ]
        ]
    ],
];
