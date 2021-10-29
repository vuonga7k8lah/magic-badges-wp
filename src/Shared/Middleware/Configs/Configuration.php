<?php
return apply_filters(MYSHOPKIT_MB_WP_HOOK_PREFIX . 'Filter\Shared\Middleware\Configs\MyShopKitMBMiddleware',
    [
        'IsUserLoggedIn'               => 'MyShopKitMBWP\Shared\Middleware\Middlewares\IsUserLoggedIn',
        'IsShopLoggedInLowLevelCheck'  => 'MyShopKitMBWP\Shared\Middleware\Middlewares\IsShopLoggedInLowLevelCheckMiddleware',
        'IsShopLoggedInHighLevelCheck' => 'MyShopKitMBWP\Shared\Middleware\Middlewares\IsShopLoggedInHighLevelCheckMiddleware',
        'IsBadgeExist'                 => 'MyShopKitMBWP\Shared\Middleware\Middlewares\IsBadgeExistMiddleware',
        'IsBadgeTypeExist'             => 'MyShopKitMBWP\Shared\Middleware\Middlewares\IsBadgeTypeExistMiddleware',
        'IsReachedAllowedImpressions'  => 'MyShopKitMBWP\Plans\Middlewares\IsReachedAllowedImpressions',
    ]
);
