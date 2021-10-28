<?php

namespace MyShopKitMB\Product\Shared\Platform\Shopify;

use EBase\Shopify\LoginRegister\Models\CustomerShopModel;
use EBase\Shopify\Shared\Connection;
use MyShopKitMB\Product\Shared\Platform\Interfaces\IPlatform;
use PHPShopify\Exception\ApiException;
use PHPShopify\Exception\CurlException;

class ShopifyPlatform implements IPlatform
{
    protected array $aArgs    = [];
    protected array $aProduct = [];

    public function setParams(array $aParams): IPlatform
    {
        $this->aArgs = $aParams;
        return $this;
    }

    /**
     * @throws ApiException
     * @throws CurlException
     */
    public function getRawProduct(): IPlatform
    {
        $customerID = 1;
        $shopName = CustomerShopModel::getShopifyShopNameByCustomerID($customerID);
        $shopID = CustomerShopModel::getIdByCustomerIDAndShopName($customerID, $shopName);
        $accessToken = CustomerShopModel::getPlatformToken($shopID);
        $aConnect = Connection::connect($shopName, $accessToken)->getShopifySDK();
        $this->aProduct = $aConnect->Product->get([]);
        return $this;
    }

    public function getFormatProduct()
    {
        var_dump($this->aProduct);
        die();
    }
}