<?php


namespace MyShopKitMBWP\Product\Services\Post;


use MyShopKitMBWP\Shared\Post\Query\IQueryPost;
use MyShopKitMBWP\Shared\Post\Query\QueryPost;

class ProductQueryService extends QueryPost implements IQueryPost
{
    public function parseArgs(): IQueryPost
    {
        $this->aArgs = $this->commonParseArgs();

        return $this;
    }

}
