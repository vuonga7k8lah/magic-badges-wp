<?php

namespace MyShopKitMBWP\Dashboard\Shared;


use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use WilcityServiceClient\Helpers\GetSettings;

trait GeneralHelper
{
    protected string $dashboardSlug = 'dashboard';
    protected string $authSlug      = 'auth-settings';

    protected function getDashboardSlug(): string
    {
        return AutoPrefix::namePrefix($this->dashboardSlug);
    }

    protected function getAuthSlug(): string
    {
        return AutoPrefix::namePrefix($this->authSlug);
    }

    private function getToken()
    {
        $token = get_option(MYSHOPKIT_MB_WP_PREFIX . 'purchase_code');
        if (!empty($token)) {
            return $token;
        }

        if (class_exists('\WilcityServiceClient\Helpers\GetSettings')) {
            return GetSettings::getOptionField('secret_token');
        }
        return '';
    }
}
