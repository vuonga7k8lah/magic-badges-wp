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

	public static function getManualIDByProductIds($aIds, $postType): array
	{
		global $wpdb;
		$sql = $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} as posts JOIN {$wpdb->postmeta} as postmeta ON posts.ID=postmeta.post_id WHERE posts.post_type = %s AND postmeta.meta_key = 'mskmbwp_product_id'  AND postmeta.meta_value in ('" .
			implode('\',\'', $aIds) . "')", $postType
		);
		$aPostID = $wpdb->get_results($sql, ARRAY_A);

		return !empty($aPostID) ? array_map(function ($aItem) {
			return $aItem['ID'];
		}, $aPostID) : [];
	}
}
