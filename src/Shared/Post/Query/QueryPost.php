<?php


namespace MyShopKitMBWP\Shared\Post\Query;


use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use WP_Query;


class QueryPost
{

    protected array     $aArgs           = [];
    protected string    $postType        = '';
    protected ?WP_Query $oQuery;
    protected bool      $isStatusConfig  = false;
    protected bool      $isSetCountItems = false;
    private array       $aRawArgs        = [];

    public function setRawArgs(array $aRawArgs): IQueryPost
    {
        $this->aRawArgs = $aRawArgs;
        return $this;
    }

    public function commonParseArgs(): array
    {
        $this->aArgs = shortcode_atts($this->defineArgs(), $this->aRawArgs);
        if (isset($this->aArgs['status']) && !empty($this->aArgs['status'])) {
            if ($this->aArgs['status'] != 'any') {
                $this->aArgs['post_status'] = $this->aArgs['status'] == 'active' ? 'publish' : 'draft';
            } else {
                $this->aArgs['post_status'] = ['draft', 'publish'];
            }
            unset($this->aArgs['status']);
        } else {
            $this->aArgs['post_status'] = ['draft', 'publish'];
        }
        if (isset($this->aArgs['limit']) && $this->aArgs['limit'] <= 50) {
            $this->aArgs['posts_per_page'] = $this->aArgs['limit'];
            unset($this->aArgs['limit']);
        } else {
            $this->aArgs['posts_per_page'] = 200;
        }

        if (isset($this->aRawArgs['page']) && $this->aRawArgs['page']) {
            $this->aArgs['paged'] = $this->aRawArgs['page'];
        } else {
            $this->aArgs['paged'] = 1;
        }

        if (empty($this->aArgs['s'])) {
            unset($this->aArgs['s']);
        }
        if (!empty($this->aRawArgs['postType']) && isset($this->aRawArgs['postType'])) {
            $this->aArgs['post_type'] = $this->aRawArgs['postType'];
        }
        if (isset($this->aRawArgs['taxSlugs']) && !empty($this->aRawArgs['taxSlugs']) &&
            !empty($this->aRawArgs['taxName'])) {
            $aTaxSlugs = array_map(function ($key) {
                return trim($key);
            }, explode(',', $this->aRawArgs['taxSlugs']));
            $this->aArgs['tax_query'] = [
                [
                    'taxonomy' => AutoPrefix::namePrefix($this->aRawArgs['taxName']),
                    'field'    => 'slug',
                    'terms'    => $aTaxSlugs,
                ]
            ];
        }
        if (!empty($this->aArgs['ids'])) {
            $this->aArgs['post__in'] = explode(',', $this->aArgs['ids']);
        } else {
            if (!empty($this->aArgs['id'])) {
                $this->aArgs['p'] = $this->aArgs['id'];
            }
        }

        unset($this->aArgs['ids']);
        unset($this->aArgs['id']);

        return $this->aArgs;
    }

    private function defineArgs(): array
    {
        return [
            'ids'     => 0,
            'id'      => 0,
            'limit'   => 50,
            'paged'   => 1,
            'author'  => 0,
            'orderby' => 'name',
            'order'   => 'ASC',
            's'       => '',
            'status'  => 'any',
        ];
    }

    /**
     * @param PostSkeleton $oPostSkeleton
     * @param string $pluck
     * @param bool $isSingle
     *
     * @return array
     */
    public function query(PostSkeleton $oPostSkeleton, string $pluck = '', bool $isSingle = false): array
    {
        $this->oQuery = new WP_Query($this->aArgs);
        $aResponse['maxPages'] = 0;
        $aResponse['items'] = [];

        if (!$this->oQuery->have_posts()) {
            wp_reset_postdata();

            return MessageFactory::factory()->success(
                esc_html__('We found no items', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                $aResponse
            );
        }
        if ($this->isSetCountItems) {

            return MessageFactory::factory()->success(
                sprintf(esc_html__('We found %s items', MYSHOPKIT_MB_WP_REST_NAMESPACE), $this->oQuery->found_posts),
                ['countPosts' => $this->oQuery->found_posts]
            );
        }
        $aItems = [];
        while ($this->oQuery->have_posts()) {
            $this->oQuery->the_post();
            if ($this->isStatusConfig) {
                $postID = $this->oQuery->post->ID;
                $aPostMeta = get_post_meta($postID, AutoPrefix::namePrefix('config'), true);
                $badgeUrl = get_post_meta($postID, AutoPrefix::namePrefix('badge_url'), true);
                $productID = get_post_meta($postID, AutoPrefix::namePrefix('product_id'), true);
                $aItems[$productID] = [
                    'id'        => (string)$postID,
                    'config'    => $aPostMeta,
                    'productID' => $productID,
                    'status'    => get_post_status($postID) == 'publish',
                    'urlImage'  => $badgeUrl
                ];
            } else {
                $aItems[] = $oPostSkeleton->setPost($this->oQuery->post)->getPostData($pluck);
            }
        }
        $aResponse['maxPages'] = $this->oQuery->max_num_pages;
        if (!$isSingle) {
            $aResponse['items'] = $aItems;
        } else {
            $aResponse = array_merge($aItems[0], $aResponse);
        }

        if ($isSingle) {
            unset($aResponse['maxPages']);
        }

        return MessageFactory::factory()->success(
            sprintf(esc_html__('We found %s items', MYSHOPKIT_MB_WP_REST_NAMESPACE), count($aItems)),
            $aResponse
        );
    }

    public function getArgs(): array
    {
        return $this->aArgs;
    }

    public function setConfigKeyTitle(bool $isStatusConfig): QueryPost
    {
        if ($isStatusConfig) {
            $this->isStatusConfig = $isStatusConfig;
        }
        return $this;
    }

    public function setCountItems(bool $isSetCountItems): QueryPost
    {
        if ($isSetCountItems) {
            $this->isSetCountItems = $isSetCountItems;
        }
        return $this;
    }
}
