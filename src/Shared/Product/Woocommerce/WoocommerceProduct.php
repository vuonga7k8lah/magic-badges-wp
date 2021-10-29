<?php

namespace MyShopKitMBWP\Shared\Product\Woocommerce;

use MyShopKitMBWP\Product\Models\ProductMetaModel;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Product\Services\Post\WCQueryService;
use MyShopKitMBWP\Shared\Product\Interfaces\IPlatform;
use WC_Product;
use WP_Post;
use WP_Query;

class WoocommerceProduct implements IPlatform
{
    protected string $regularPriceKey = '_regular_price';
    protected string $salePriceKey    = '_sale_price';
    protected string $productImageKey = '_product_image_gallery';

    public function getProductsBySlug(array $aSlug, $customerID)
    {
        // TODO: Implement getProductsBySlug() method.
    }

    public function parseProducts(array $aProducts): array
    {
        // TODO: Implement parseProducts() method.
    }

    public function search($titleKeyword, $customerID, array $aArgs = [], $isExtract = false): array
    {
        // TODO: Implement search() method.
    }

    public function getProducts($customerID, array $aArgs = []): array
    {

        $aArgs = wp_parse_args($aArgs, [
            'post_status' => 'publish',
            'limit'       => 50,
            'post_type'   => ['product'],
            'order'       => 'ASC',
            'orderby'     => 'title'
        ]);

        if (isset($aArgs['page']) && $aArgs['page']) {
            $aArgs['paged'] = $aArgs['page'];
        } else {
            $aArgs['paged'] = 1;
        }
        unset($aArgs['page']);

        if (!empty($aArgs['limit'])) {
            // $aArgs['posts_per_page'] = $aArgs['limit'];
            $aArgs['posts_per_page'] = 20;
            unset($aArgs['limit']);
        }

        if (isset($aArgs['s']) && !empty($aArgs['s'])) {
            $aArgs['sentence'] = true;
        } else {
            unset($aArgs['s']);
        }

        $oQuery = new WP_Query($aArgs);
        if (!$oQuery->have_posts()) {
            wp_reset_postdata();
            return MessageFactory::factory()->success(
                esc_html__('We found no product', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                [
                    'items' => []
                ]
            );
        }

        /**
         * @var WP_Post $aCoupon
         */

        while ($oQuery->have_posts()) {
            $oQuery->the_post();
            $id = $oQuery->post->ID;
            $aIdProductImage = explode(',', get_post_meta($id, $this->productImageKey, true));
            $idProductImage = $aIdProductImage[0] ?: get_post_meta($id, '_thumbnail_id', true);
            $aProductImage = wp_get_attachment_image_src($idProductImage, 'auto');

            $aImage = [
                'src'    => $aProductImage ? $aProductImage[0] : '',
                'width'  => $aProductImage ? $aProductImage[1] : '',
                'height' => $aProductImage ? $aProductImage[2] : ''
            ];

            $aPrice = [
                (string)get_post_meta($id, $this->regularPriceKey, true) ?: '0',
                (string)get_post_meta($id, $this->salePriceKey, true) ?: '0',
            ];
            $aItems[] = [
                'id'         => $id,
                'outOfStock' => ProductMetaModel::getStockStatus($id) === 'outofstock',
                'title'      => $oQuery->post->post_title,
                'slug'       => $oQuery->post->post_name,
                'link'       => get_permalink($id),
                'image'      => $aImage,
                'price'      => $aPrice,
            ];
        }
        $maxPages = $oQuery->max_num_pages;
        wp_reset_postdata();

        return MessageFactory::factory()->success(
            sprintf(esc_html__('We found %s items', MYSHOPKIT_MB_WP_REST_NAMESPACE), count($aItems)),
            [
                'items'    => $aItems,
                'maxPages' => $maxPages
            ]
        );

    }

    public function getProductsByProductID($aProductIDs, $customerID, array $aArgs = []): array
    {
        $aArgs = wp_parse_args($aArgs, [
            'post_status' => 'publish',
            'limit'       => 50,
            'post_type'   => ['product'],
            'order'       => 'ASC',
            'orderby'     => 'title'
        ]);
        if (isset($aArgs['s']) && !empty($aArgs['s'])) {
            $aArgs['sentence'] = true;
        }
        if (!empty($aProductIDs)) {
            $aArgs['post__in'] = $aProductIDs;
        }
        if (isset($aArgs['page']) && $aArgs['page']) {
            $aArgs['paged'] = $aArgs['page'];
        } else {
            $aArgs['paged'] = 1;
        }
        unset($aArgs['page']);

        if (!empty($aArgs['limit'])) {
            $aArgs['posts_per_page'] = $aArgs['limit'];
            unset($aArgs['limit']);
        }

        $oQuery = new WP_Query($aArgs);

        if (!$oQuery->have_posts()) {
            wp_reset_postdata();
            return MessageFactory::factory()->success(
                esc_html__('We found no product', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                [
                    'items' => []
                ]
            );
        }

        /**
         * @var WP_Post $aCoupon
         */

        while ($oQuery->have_posts()) {
            $oQuery->the_post();
            $id = $oQuery->post->ID;
            $aIdProductImage = explode(',', get_post_meta($id, $this->productImageKey, true));
            $idProductImage = $aIdProductImage[0] ?: get_post_meta($id, '_thumbnail_id', true);
            $aProductImage = wp_get_attachment_image_src($idProductImage, 'auto');

            $aImage = [
                'src'    => $aProductImage ? $aProductImage[0] : '',
                'width'  => $aProductImage ? $aProductImage[1] : '',
                'height' => $aProductImage ? $aProductImage[2] : ''
            ];

            $aPrice = [
                (string)get_post_meta($id, $this->regularPriceKey, true) ?: '0',
                (string)get_post_meta($id, $this->salePriceKey, true) ?: '0',
            ];
            $aItems[] = [
                'id'         => $id,
                'outOfStock' => ProductMetaModel::getStockStatus($id) === 'outofstock',
                'title'      => $oQuery->post->post_title,
                'slug'       => $oQuery->post->post_name,
                'link'       => get_permalink($id),
                'image'      => $aImage,
                'price'      => $aPrice,
            ];
        }
        $maxPages = $oQuery->max_num_pages;
        wp_reset_postdata();

        return MessageFactory::factory()->success(
            sprintf(esc_html__('We found %s items', MYSHOPKIT_MB_WP_REST_NAMESPACE), count($aItems)),
            [
                'items'    => $aItems,
                'maxPages' => $maxPages
            ]
        );

    }

    public function getCost(): array
    {
        // TODO: Implement getCost() method.
    }

    public function getLastCursor(): string
    {
        // TODO: Implement getLastCursor() method.
    }

    public function hasNextPage(): bool
    {
        // TODO: Implement hasNextPage() method.
    }
}
