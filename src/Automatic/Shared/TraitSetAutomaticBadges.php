<?php

namespace MyShopKitMB\Automatic\Shared;

trait TraitSetAutomaticBadges
{
	private array $aAutomaticBadgesOrder
		= [
			'MyShopKitMB\Automatic\Shared\OutOfStockBadge',
			'MyShopKitMB\Automatic\Shared\OnSaleBadge',
			'MyShopKitMB\Automatic\Shared\NewArrivalBadge',
		];
}
