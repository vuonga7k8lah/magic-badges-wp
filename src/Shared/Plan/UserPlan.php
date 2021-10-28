<?php

namespace MyShopKitMB\Shared\Plan;

use MyShopKitMB\Illuminate\Prefix\AutoPrefix;
use MyShopKitMB\Plans\Shared\ThemeOption;


class UserPlan
{
    private static string $userPlanMetaKey = 'update_plan';

    public static function getUserPlanMetaKey(): string
    {
        return AutoPrefix::namePrefix(self::$userPlanMetaKey);
    }

    public static function getCurrentCustomerPlanID($userID): int
    {
        $plan = self::getCurrentCustomerPlan($userID);
        if ($post = get_page_by_path($plan, OBJECT, AutoPrefix::namePrefix('plan'))) {
            $id = $post->ID;
        } else {
            $id = 0;
        }

        return $id;
    }

    public static function getCurrentCustomerPlan($userID)
    {
        $aUserPlan = get_user_meta(
            $userID,
            AutoPrefix::namePrefix(self::$userPlanMetaKey),
            true
        );

        if (!isset($aUserPlan['plan']) || empty($aUserPlan['plan'])) {
            $plan = ThemeOption::getDefaultPlan();
        } else {
            $plan = $aUserPlan['plan'];
        }

        return $plan;
    }
}
