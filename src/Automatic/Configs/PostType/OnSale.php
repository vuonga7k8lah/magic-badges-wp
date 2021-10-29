<?php

use MyShopKitMBWP\Shared\App;

$aLabels = [
    'name'                  => esc_html__( 'On Sale', MYSHOPKIT_MB_WP_REST_NAMESPACE ),
    'singular_name'         => esc_html__( 'On Sale', MYSHOPKIT_MB_WP_REST_NAMESPACE ),
    'menu_name'             => esc_html__( 'On Sale', MYSHOPKIT_MB_WP_REST_NAMESPACE ),
    'name_admin_bar'        => esc_html__( 'On Sale', MYSHOPKIT_MB_WP_REST_NAMESPACE ),
];

return [
    'labels'             => $aLabels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => [ 'slug' => App::get('listPostTypeAutomatic')['onSale'] ],
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => true,
    'menu_position'      => null,
    'supports'           => [ 'title', 'editor', 'thumbnail', 'author' ]
];
