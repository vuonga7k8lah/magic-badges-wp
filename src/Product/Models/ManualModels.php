<?php

namespace MyShopKitMBWP\Product\Models;

class ManualModels
{
    public static function isCheckPostExitsByPostName($title, $postType): bool
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s", $title, $postType
        );
        $postID = $wpdb->get_var($sql);
        return !empty($postID);
    }

    public static function getManualBySlugs($aSlugs, $postType): array
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_title in ('" . implode('\',\'', $aSlugs) . "') AND 
             {$wpdb->posts}.post_type = %s",
            $postType
        );
        $aPostID = $wpdb->get_results($sql);
        return !empty($aPostID) ? $aPostID : [];
    }
}
