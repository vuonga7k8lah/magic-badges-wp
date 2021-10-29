<?php

namespace MyShopKitMBWP\Shared\Post;

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

trait TraitPostHelps
{
    public function getPostTypes()
    {
        return apply_filters(MYSHOPKIT_MB_WP_HOOK_PREFIX . 'src/Shared/Post/TraitPostHelps/getListPostType', [
            'manual'     => AutoPrefix::namePrefix('manual'),
            'badge'      => AutoPrefix::namePrefix('badge'),
            'newArrival' => AutoPrefix::namePrefix('new_arrival'),
            'onSale'     => AutoPrefix::namePrefix('on_sale'),
            'outOfStock' => AutoPrefix::namePrefix('out_of_stock'),
        ]);
    }
}
