<?php

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

$aLabels = [
    'name'      => esc_html__('Keywords', MYSHOPKIT_MB_WP_REST_NAMESPACE),
    'singular'  => esc_html__('Keyword', MYSHOPKIT_MB_WP_REST_NAMESPACE),
    'menu_name' => esc_html__('Keywords', MYSHOPKIT_MB_WP_REST_NAMESPACE),
];

return [
    'labels'            => $aLabels,
    'hierarchical'      => false,
    'taxonomyType'      => AutoPrefix::namePrefix('keyword'),
    'public'            => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_nav_menus' => true,
    'show_tagcloud'     => true,
];
