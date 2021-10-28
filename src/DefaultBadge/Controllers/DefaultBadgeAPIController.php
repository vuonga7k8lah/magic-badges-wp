<?php

namespace MyShopKitMBWP\DefaultBadge\Controllers;

use Exception;

use MyShopKitMBWP\DefaultBadge\Services\Post\BadgeQueryService;
use MyShopKitMBWP\DefaultBadge\Services\Post\CreatePostService;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Illuminate\Upload\WPUpload;
use MyShopKitMBWP\DefaultBadge\Shared\Post\BadgeSkeleton;
use WP_REST_Request;

class DefaultBadgeAPIController
{
    protected string $postType = '';
    protected string $taxonomy = '';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoute']);
        $this->postType = AutoPrefix::namePrefix('badge');
        $this->taxonomy = AutoPrefix::namePrefix('keyword');
        add_filter('upload_mimes', [$this, 'addMimeTypes']);
        add_filter('manage_posts_columns', [$this, 'addColumnImage']);
        add_action('manage_posts_custom_column', [$this, 'customColumnsImage'], 1, 2);
        add_action('manage_pages_custom_column', [$this, 'customColumnsImage'], 1, 2);
    }

    public function addColumnImage($aColumns)
    {
        if (isset($_GET['post_type']) && ($_GET['post_type'] == $this->postType)) {
            $aColumns = array_merge($aColumns, [
                'badgeIMG' => esc_html__('Featured', MYSHOPKIT_MB_WP_REST_NAMESPACE),
            ]);
        }
        return $aColumns;
    }

    public function customColumnsImage($column_name, $id)
    {
        if ('badgeIMG' == $column_name) {
            echo '<img style="width:100px;height:100px" src="'.esc_url(get_the_post_thumbnail_url($id)).'">';
        }
    }

    public function addMimeTypes($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;
    }

    public function registerRoute()
    {
        register_rest_route(MYSHOPKIT_MB_WP_REST, 'default-badges', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'getBadges'],
                'permission_callback' => '__return_true'
            ],
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'createBadges'],
                'permission_callback' => '__return_true'
            ]

        ]);
    }

    public function getBadges(WP_REST_Request $oRequest)
    {
        try {
            if (empty(get_current_user_id())) {
                throw new Exception(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $aResponse = (new BadgeQueryService())->setRawArgs($oRequest->get_params())->parseArgs()
                ->query(new BadgeSkeleton(), 'id,urlImage,taxonomy');
            if ($aResponse['status'] === 'error') {
                return MessageFactory::factory('rest')->error(
                    esc_html__('Sorry, We could not find your badge', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                    $aResponse['code']
                );
            }
            if (empty($aData = $aResponse['data']['items'])) {
                return MessageFactory::factory('rest')->success(esc_html__('We not found badge',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), [
                    'items'   => $aData,
                    'maxPage' => 0
                ]);
            }
            return MessageFactory::factory('rest')->success(sprintf(esc_html__('We found %d badges',
                MYSHOPKIT_MB_WP_REST_NAMESPACE), count($aData)),
                [
                    'items'   => $aData,
                    'maxPage' => $aResponse['data']['maxPages']
                ]);
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function createBadges(WP_REST_Request $oRequest)
    {
        try {
            $aKeywords = array_map(function ($keyword) {
                return trim($keyword);
            }, explode(',', $oRequest->get_param('keywords')));
            //define user upload username: vuongkma
            if (get_current_user_id() != 1) {
                throw new Exception(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $oUpload = new WPUpload();
            $aFileInfo = $oRequest->get_file_params();

            $isSingular = isset($aFileInfo['tmp_name']);

            if (empty($aFileInfo)) {
                return MessageFactory::factory('rest')
                    ->error(
                        esc_html__('The file is required', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                        422
                    );
            }

            $oUpload->isSingleUpload($isSingular)
                ->setFile($aFileInfo);

            if (!empty($source)) {
                $oUpload->setImageSource($source);
            }

            $aResponse = $oUpload->processUpload();
            if ($aResponse['status'] == 'error') {
                return MessageFactory::factory('rest')->error(
                    $aResponse['message'], $aResponse['code']
                );
            }

            $attachmentId = $aResponse['data']['items'][0]['id'];
            $aPostResponse = (new CreatePostService())->setRawData([
                'post_title' => uniqid('magic_badge_wp'),
                'postType'   => $this->postType
            ])->performSaveData();

            if ($aPostResponse['status'] == 'error') {
                throw new Exception($aPostResponse['message'], $aPostResponse['code']);
            }
            $postID = $aPostResponse['data']['id'];
            set_post_thumbnail($postID, $attachmentId);
            wp_set_object_terms($postID, $aKeywords, $this->taxonomy);
            return MessageFactory::factory('rest')->success($aPostResponse['message'], [
                'id' => $postID
            ]);
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }
}
