<?php

namespace MyShopKitMBWP\Automatic\Controllers;



use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Shared\App;

class AutomaticRegistration
{
    public function __construct()
    {
        add_action('cmb2_admin_init', [$this, 'registerBox']);
        add_action('init', [$this, 'registerAutomaticBadges']);
        App::bind('listPostTypeAutomatic', include plugin_dir_path(__FILE__) . '../Configs/PostType/ListPostType.php');
        add_filter(MYSHOPKIT_MB_WP_PREFIX . 'src/Shared/Post/TraitPostHelps/getListPostType',
            [$this, 'handleAddPostType']);
    }

    public function handleAddPostType($aPostType): array
    {
        return array_merge($aPostType, App::get('listPostTypeAutomatic'));
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

    public function registerAutomaticBadges()
    {
        foreach (App::get('listPostTypeAutomatic') as $key => $postType) {
            register_post_type(
                $postType,
                include plugin_dir_path(__FILE__) . '../Configs/PostType/' . ucfirst($key) . '.php'
            );
        }
    }
}
