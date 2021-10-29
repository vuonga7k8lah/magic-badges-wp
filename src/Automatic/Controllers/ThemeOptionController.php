<?php


namespace MyShopKitMB\Automatic\Controllers;


use Redux;

class ThemeOptionController
{
    public function __construct()
    {
        add_action(
            'wiloke-shopify-login/src/Controllers/ThemeOptionsController/after/app-settings',
            [$this, 'addThemeOptionSettings']
        );
    }

    public function addThemeOptionSettings($optionName)
    {
        $aListOptionConfig = array_values(include plugin_dir_path(__FILE__) . '../Configs/ThemeOption.php');
        foreach ($aListOptionConfig as $aOption) {
            Redux::set_section(
                $optionName,
                $aOption
            );
        }
    }
}
