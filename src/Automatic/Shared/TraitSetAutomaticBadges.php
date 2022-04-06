<?php

namespace MyShopKitMBWP\Automatic\Shared;

trait TraitSetAutomaticBadges
{
	private array $aAutomaticBadgesOrder
		= [
			'MyShopKitMBWP\Automatic\Shared\OutOfStockBadge',
			'MyShopKitMBWP\Automatic\Shared\OnSaleBadge',
			'MyShopKitMBWP\Automatic\Shared\NewArrivalBadge',
		];
}
