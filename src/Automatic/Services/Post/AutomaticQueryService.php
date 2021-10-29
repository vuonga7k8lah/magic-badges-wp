<?php

namespace MyShopKitMBWP\Automatic\Services\Post;

use MyShopKitMBWP\Shared\Post\Query\IQueryPost;
use MyShopKitMBWP\Shared\Post\Query\QueryPost;

class AutomaticQueryService extends QueryPost implements IQueryPost
{

    public function parseArgs(): IQueryPost
    {
        $this->aArgs = $this->commonParseArgs();
        return $this;
    }
}
