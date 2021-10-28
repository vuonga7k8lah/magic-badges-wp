<?php

namespace MyShopKitMBWP\DefaultBadge\Services\Post;


use MyShopKitMBWP\Shared\Post\Query\IQueryPost;
use MyShopKitMBWP\Shared\Post\Query\QueryPost;

class BadgeQueryService extends QueryPost implements IQueryPost
{
    public function parseArgs(): IQueryPost
    {
        $this->aArgs = $this->commonParseArgs();
        $this->aArgs['post_type'] = $this->getPostType();

        return $this;
    }

    public function getPostType(): string
    {
        $aConfig = include plugin_dir_path(__FILE__) . '../../Configs/PostType.php';
        $this->postType = $aConfig['postType'];

        return $this->postType;
    }
}
