<?php

namespace MyShopKitMBWP\Illuminate\Prefix;

class AutoPrefix {
	public static function namePrefix( $name ) {
		return strpos( $name, MYSHOPKIT_MB_WP_PREFIX ) === 0 ? $name : MYSHOPKIT_MB_WP_PREFIX . $name;
	}

	public static function removePrefix( string $name ): string {
		if ( strpos( $name, MYSHOPKIT_MB_WP_PREFIX ) === 0 ) {
			$name = str_replace( MYSHOPKIT_MB_WP_PREFIX, '', $name );
		}

		return $name;
	}
}
