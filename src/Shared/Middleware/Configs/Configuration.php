<?php
return apply_filters(MYSHOPKIT_MB_HOOK_PREFIX . 'Filter\Shared\Middleware\Configs\MyShopKitMBMiddleware',
    [
        'IsUserLoggedIn'               => 'MyShopKitMB\Shared\Middleware\Middlewares\IsUserLoggedIn',
        'IsShopLoggedInLowLevelCheck'  => 'MyShopKitMB\Shared\Middleware\Middlewares\IsShopLoggedInLowLevelCheckMiddleware',
        'IsShopLoggedInHighLevelCheck' => 'MyShopKitMB\Shared\Middleware\Middlewares\IsShopLoggedInHighLevelCheckMiddleware',
        'IsBadgeExist'                 => 'MyShopKitMB\Shared\Middleware\Middlewares\IsBadgeExistMiddleware',
        'IsBadgeTypeExist'             => 'MyShopKitMB\Shared\Middleware\Middlewares\IsBadgeTypeExistMiddleware',
        'IsReachedAllowedImpressions'             => 'MyShopKitMB\Plans\Middlewares\IsReachedAllowedImpressions',
    ]
);
