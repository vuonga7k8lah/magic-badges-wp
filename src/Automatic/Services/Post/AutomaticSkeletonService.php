<?php


namespace MyShopKitMBWP\Automatic\Services\Post;


use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Shared\Post\Query\PostSkeleton;

class AutomaticSkeletonService extends PostSkeleton
{

    public function getContent(): string
    {
        return get_the_content($this->oPost->ID);
    }

    public function getUrlImage(): string
    {
        return get_post_meta($this->oPost->ID,AutoPrefix::namePrefix('badgeUrl'),true);
    }

    public function getPostType(): string
    {
        return get_post_type($this->oPost->ID);
    }
}
