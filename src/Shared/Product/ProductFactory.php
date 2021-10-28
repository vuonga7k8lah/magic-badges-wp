<?php

namespace MyShopKitMBWP\Shared\Product;

use Exception;
use MyShopKitMBWP\Shared\Product\Interfaces\IPlatform;

class ProductFactory
{

	/**
	 * @throws Exception
	 */
	public static function setPlatform(string $platform): IPlatform
	{
		$aConfigPlatform = include plugin_dir_path(__FILE__) . 'Configs/Platform.php';
		if (array_key_exists($platform, $aConfigPlatform) && class_exists
			($className = $aConfigPlatform[$platform])) {
			return new $className;
		} else {
			throw new Exception("Sorry, the platform not exist");
		}
	}
}
