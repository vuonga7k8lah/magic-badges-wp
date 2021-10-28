<?php

namespace MyShopKitMBWP\DefaultBadge\Shared\Post;

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Shared\Post\Query\PostSkeleton;

class BadgeSkeleton extends PostSkeleton
{
    public function getUrlImage(): string
    {
        $postID = $this->oPost->ID;
        return get_the_post_thumbnail_url($postID);
    }

    public function getTaxonomy(): array
    {
        $taxonomyType = (include plugin_dir_path(__FILE__) . '../../Configs/Taxonomy.php')['taxonomyType'];
        $aSlug = [];
        $taxonomy = '';
        $aTerms = wp_get_post_terms($this->oPost->ID, $taxonomyType);
        foreach ($aTerms as $oTerm) {
            $aSlug[] = $oTerm->slug;
            $taxonomy = $oTerm->taxonomy;
        }
        return [
            'slugs' => $aSlug,
            'name'  => AutoPrefix::removePrefix($taxonomy)
        ];
    }

}
