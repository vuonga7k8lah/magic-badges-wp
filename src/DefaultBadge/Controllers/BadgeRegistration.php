<?php

namespace MyShopKitMBWP\DefaultBadge\Controllers;

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class BadgeRegistration
{
    public function __construct()
    {
        add_action('init', [$this, 'registerBadge']);
    }

    public function registerBadge()
    {
        $aConfig = include plugin_dir_path(__FILE__) . '../Configs/PostType.php';
        register_post_type(
            $aConfig['postType'],
            $aConfig
        );
        $aConfigTaxonomy = include plugin_dir_path(__FILE__) . '../Configs/Taxonomy.php';
        register_taxonomy(
            $aConfigTaxonomy['taxonomyType'],
            $aConfig['postType'],
            $aConfigTaxonomy
        );
    }

}
