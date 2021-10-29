<?php

use MyShopKitMB\Automatic\Shared\ThemeOption;

return [
    'out_of_stock' => [
        'id'          => '',
        'config'      => null,
        'postType'    => 'out_of_stock',
        'badge_id'    => ThemeOption::getBadgeID('out_of_stock'),
        'urlImage'    => get_the_post_thumbnail_url(ThemeOption::getBadgeID('out_of_stock')),
        'title'       => ThemeOption::getTitle('out_of_stock'),
        'description' => ThemeOption::getDescription('out_of_stock'),
        'isSelected'  => false
    ],
    'on_sale'      => [
        'id'          => '',
        'config'      => null,
        'postType'    => 'on_sale',
        'badge_id'    => ThemeOption::getBadgeID('on_sale'),
        'urlImage'    => get_the_post_thumbnail_url(ThemeOption::getBadgeID('on_sale')),
        'title'       => ThemeOption::getTitle('on_sale'),
        'description' => ThemeOption::getDescription('on_sale'),
        'isSelected'  => false
    ],
    'new_arrival'  => [
        'id'          => '',
        'config'      => null,
        'postType'    => 'new_arrival',
        'badge_id'    => ThemeOption::getBadgeID('new_arrival'),
        'urlImage'    => get_the_post_thumbnail_url(ThemeOption::getBadgeID('new_arrival')),
        'title'       => ThemeOption::getTitle('new_arrival'),
        'description' => ThemeOption::getDescription('new_arrival'),
        'isSelected'  => false
    ]
];
