<?php

namespace MyShopKitMB\Automatic\Shared;

use EBase\Helpers\ThemeOptions;

class ThemeOption
{

    public static function getOptionField($field, $typeAutomatic, $default = '')
    {
        $aThemeOptions = ThemeOptions::getOptions();
        switch ($typeAutomatic) {
            case 'new_arrival':
                $field = strpos($field, 'default-new-arrival') !== false ? $field : 'default-new-arrival' . $field;
                break;
            case 'out_of_stock':
                $field = strpos($field, 'default-out-of-stock') !== false ? $field : 'default-out-of-stock' . $field;
                break;
            case 'on_sale':
                $field = strpos($field, 'default-on-sale') !== false ? $field : 'default-on-sale' . $field;
                break;
        }

        return $aThemeOptions[$field] ?? $default;
    }

    public static function getBadgeID($typeAutomatic): string
    {
        $result = self::getOptionField('badgeID', $typeAutomatic);

        return $result ?: '';
    }

    public static function getTitle($typeAutomatic): string
    {
        $result = self::getOptionField('title', $typeAutomatic);

        return $result ?: '';
    }

    public static function getDescription($typeAutomatic): string
    {
        $result = self::getOptionField('description', $typeAutomatic);
        return $result ?: '';
    }
}
