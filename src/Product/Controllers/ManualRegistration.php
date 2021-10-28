<?php

namespace MyShopKitMBWP\Product\Controllers;



use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class ManualRegistration
{
    public function __construct()
    {
        add_action('cmb2_admin_init', [$this, 'registerBox']);
        add_action('init', [$this, 'registerManual']);
    }

    public function registerBox()
    {
        $aConfig = include plugin_dir_path(__FILE__) . '../Configs/PostMeta.php';

        foreach ($aConfig as $aSection) {
            $aFields = $aSection['fields'];
            unset($aSection['fields']);
            $oCmb = new_cmb2_box($aSection);
            foreach ($aFields as $aField) {
                $aField['id'] = AutoPrefix::namePrefix($aField['id']);
                $oCmb->add_field($aField);
            }
        }
    }

    public function registerManual()
    {
        $aConfig = include plugin_dir_path(__FILE__) . '../Configs/PostType.php';
        register_post_type(
            $aConfig['postType'],
            $aConfig
        );
    }
}
