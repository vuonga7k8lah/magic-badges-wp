<?php

namespace MyShopKitMBWP\Product\Models;

class ProductMetaModel
{
    private static string $tableName = 'wc_product_meta_lookup';

    public static function getStockStatus($productID): ?string
    {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT stock_status FROM " . $wpdb->prefix . self::$tableName .
            " WHERE product_id=%d", $productID));
    }
}
