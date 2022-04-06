<?php

namespace MyShopKitMBWP\Automatic\Shared;

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

trait TraitUpdateInterval
{
	public string $metaKeyInterval = 'interval';

	public function handleUpdateInterval(int $postID, int $interval): bool|int
	{
		return update_post_meta($postID, AutoPrefix::namePrefix($this->metaKeyInterval), $interval);
	}

	public function getInterval(int $postID)
	{
		return get_post_meta($postID, AutoPrefix::namePrefix($this->metaKeyInterval), true) ?: '7';
	}
}
