<?php

namespace MyShopKitMBWP\Automatic\Controllers;


use Exception;
use MyShopKitMBWP\Automatic\Services\Post\AutomaticQueryService;
use MyShopKitMBWP\Automatic\Services\Post\AutomaticSkeletonService;
use MyShopKitMBWP\Automatic\Services\Post\CreatePostService;
use MyShopKitMBWP\Automatic\Services\Post\DeletePostService;
use MyShopKitMBWP\Automatic\Services\Post\UpdatePostService;
use MyShopKitMBWP\Automatic\Services\PostMeta\AddPostMetaService;
use MyShopKitMBWP\Automatic\Services\PostMeta\UpdatePostMetaService;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Shared\App;
use MyShopKitMBWP\Shared\Middleware\TraitMainMiddleware;
use MyShopKitMBWP\Shared\Post\TraitIsPostAuthor;
use WP_REST_Request;

class AutomaticBadgeController
{
    use TraitMainMiddleware, TraitIsPostAuthor;

    protected array $aPriority = ['out_of_stock', 'on_sale', 'new_arrival'];

    public function __construct()
    {
        add_filter(MYSHOPKIT_MB_WP_HOOK_PREFIX .
            'Filter/Automatic/Controllers/AutomaticBadgeController/getAutomaticProduct',
            [$this, 'handleDetectBadge'], 10, 2);
        add_action('rest_api_init', [$this, 'registerRouters']);
    }

    public function registerRouters()
    {
        register_rest_route(MYSHOPKIT_MB_WP_REST, 'automatics',
            [
                [
                    'methods'             => 'POST',
                    'callback'            => [$this, 'createAutomatic'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getAutomatics'],
                    'permission_callback' => '__return_true'
                ]
            ]
        );

        register_rest_route(MYSHOPKIT_MB_WP_REST, 'automatics/(?P<id>(\d+))',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getProduct'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'PUT',
                    'callback'            => [$this, 'updateAutomatic'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'PATCH',
                    'callback'            => [$this, 'updateAutomatic'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'DELETE',
                    'callback'            => [$this, 'deleteAutomatic'],
                    'permission_callback' => '__return_true'
                ]
            ]
        );
    }

    public function createAutomatic(WP_REST_Request $oRequest)
    {
        try {
            $postType = !empty($oRequest->get_param('postType')) ?
                AutoPrefix::namePrefix($oRequest->get_param('postType')) : '';
            $aResponseMiddleware = $this->processMiddleware(
                [
                    'IsUserLoggedIn',
                    'IsBadgeTypeExist'
                ],
                [
                    'postType' => $postType
                ]
            );

            if ($aResponseMiddleware['status'] == 'error') {
                throw new Exception($aResponseMiddleware['message'], 401);
            }

            $aCheckUserCreatedAutomatic = $this->checkIsUserCreatedAutomatic(get_current_user_id(), $postType);

            if ($aCheckUserCreatedAutomatic['status'] == 'error') {
                throw new Exception($aCheckUserCreatedAutomatic['message'], 401);
            }
            if ($aCheckUserCreatedAutomatic['data']['status']) {
                throw new Exception(esc_html__('You already set the badge for this type of product.',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $aPostResponse = (new CreatePostService())->setRawData(array_merge(
                $oRequest->get_params(), [
                    'postType' => $postType
                ]
            ))->performSaveData();

            if ($aPostResponse['status'] == 'error') {
                throw new Exception($aPostResponse['message'], $aPostResponse['code']);
            }

            $aResponse = (new AddPostMetaService())->setID($aPostResponse['data']['id'])
                ->addPostMeta($oRequest->get_params());

            if ($aResponse['status'] == 'error') {
                throw new Exception($aResponse['message'], $aResponse['code']);
            }
            return MessageFactory::factory('rest')->success($aPostResponse['message'],
                [
                    'id'   => (string)$aPostResponse['data']['id'],
                    'date' => (string)strtotime(get_the_date('Y-m-d H:i:s', $aPostResponse['data']['id']))
                ]
            );
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function checkIsUserCreatedAutomatic(string $userID, string $postType)
    {
        $aResponse = (new AutomaticQueryService())->setRawArgs(
            [
                'postType' => $postType,
                'author'   => $userID,
                'limit'    => 1
            ]
        )->parseArgs()->query(new AutomaticSkeletonService(), 'id');
        if ($aResponse['status'] == 'error') {
            return MessageFactory::factory()->error($aResponse['message'], 400, [
                'status' => false
            ]);
        }
        return MessageFactory::factory()->success('Passed', [
            'status' => !empty($aResponse['data']['items'])
        ]);
    }

    public function updateAutomatic(WP_REST_Request $oRequest)
    {
        try {
            $postID = $oRequest->get_param('id');
            $postType = get_post_type($postID);
            $aResponseMiddleware = $this->processMiddleware(
                [
                    'IsUserLoggedIn',
                    'IsBadgeTypeExist',
                    'IsBadgeExist'
                ],
                [
                    'postType' => $postType,
                    'postID'   => $postID
                ]
            );
            if ($aResponseMiddleware['status'] == 'error') {
                throw new Exception($aResponseMiddleware['message'], 401);
            }

            $aPostResponse = (new UpdatePostService())
                ->setID($postID)
                ->setRawData(array_merge($oRequest->get_params(), [
                    'postType' => $postType
                ]))
                ->performSaveData();

            if ($aPostResponse['status'] == 'error') {
                return MessageFactory::factory('rest')->error($aPostResponse['message'], $aPostResponse['code']);
            }

            $aResponse = (new UpdatePostMetaService())
                ->setID($aPostResponse['data']['id'])
                ->updatePostMeta($oRequest->get_params());

            if ($aResponse['status'] == 'error') {
                return MessageFactory::factory('rest')->error($aResponse['message'], $aResponse['code']);
            }

            return MessageFactory::factory('rest')
                ->success($aPostResponse['message'],
                    [
                        'id'   => (string)$aPostResponse['data']['id'],
                        'date' => (string)strtotime(get_the_modified_date('Y-m-d H:i:s', $aPostResponse['data']['id']))
                    ]);

        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function deleteAutomatic(WP_REST_Request $oRequest)
    {
        try {
            $postID = $oRequest->get_param('id');
            $postType = get_post_type($postID);
            $aResponseMiddleware = $this->processMiddleware(
                [
                    'IsUserLoggedIn',
                    'IsBadgeTypeExist',
                    'IsBadgeExist'
                ],
                [
                    'postType' => $postType,
                    'postID'   => $postID
                ]
            );

            $this->isPostAuthor($postID);

            if ($aResponseMiddleware['status'] == 'error') {
                throw new Exception($aResponseMiddleware['message'], 401);
            }
            $aPostResponse = (new DeletePostService())->setID($postID)->setPostType($postType)->delete();
            if ($aPostResponse['status'] == 'error') {
                return MessageFactory::factory('rest')->error($aPostResponse['message'], $aPostResponse['code']);
            }
            return MessageFactory::factory('rest')->success(
                $aPostResponse['message'],
                [
                    'id' => $aPostResponse['data']['id'],
                    //'urlImage' => get_the_post_thumbnail_url(ThemeOption::getBadgeID(AutoPrefix::removePrefix($postType)))
                ]
            );
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function getAutomatics(WP_REST_Request $oRequest)
    {

        $aDataAutomatic = [];
        if (empty(get_current_user_id())) {
            return MessageFactory::factory('rest')
                ->error(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
        }
        $aData = $oRequest->get_params();
        $aResponse = (new AutomaticQueryService())->setRawArgs(
            array_merge(
                $aData,
                [
                    'postType' => array_values(App::get('listPostTypeAutomatic')),
                    'author'   => get_current_user_id()
                ]
            )
        )->parseArgs()
            ->query(new AutomaticSkeletonService(), 'id,title,config,content,urlImage,badgeID,postType,status');
        if ($aResponse['status'] === 'error') {
            return MessageFactory::factory('rest')->error(
                $aResponse['message'],
                $aResponse['code']
            );
        }

        $aDefaultAuto = include plugin_dir_path(__FILE__) . '../Configs/DefaultAuto.php';

        if (!empty($aResponseAutomatic = $aResponse['data']['items'])) {

            foreach ($aResponseAutomatic as $aAutomatic) {
                $postTypeKey = AutoPrefix::removePrefix($aAutomatic['postType']);
                if (in_array($postTypeKey, $this->aPriority)) {
                    $aDataAutomatic[$postTypeKey] = [
                        'id'          => $aAutomatic['id'],
                        'config'      => $aAutomatic['config'],
                        'badge_id'    => $aAutomatic['badgeID'],
                        'urlImage'    => $aAutomatic['urlImage'],
                        'title'       => $aAutomatic['title'],
                        'postType'    => $postTypeKey,
                        'description' => $aAutomatic['content'],
                        'isSelected'  => $aAutomatic['status'] == 'active'
                    ];
                }
            }
        }
        return MessageFactory::factory()->success(esc_html__('we found list badges automatic',
            MYSHOPKIT_MB_WP_REST_NAMESPACE), [
            'items' => array_values(wp_parse_args($aDataAutomatic, $aDefaultAuto))
        ]);
    }

    public function handleDetectBadge(array $aListProduct, string $userID): array
    {
        $aData = [];
        if (!empty($aListProduct['data']['items'])) {
            foreach ($aListProduct['data']['items'] as $aProduct) {
                $aResponse = $this->detectBadge($aProduct, $userID);
                $aData[$aProduct['handle']] = ($aResponse['status'] == 'success') ? $aResponse['data']['badge'] : [];
            }
        }
        return $aData;
    }

    public function detectBadge(array $aProduct, string $userID)
    {

        $oAutomaticBadgeContext = new AutomaticContext();
        $oAutomaticBadgeContext->setProductInfo($aProduct)->setUserID($userID);
        try {
            while (!$oAutomaticBadgeContext->oState->isAppliedBadge()) {
                $oAutomaticBadgeContext->oState->proceedToNextBadge();
            }
            return MessageFactory::factory()->success(esc_html__('list data', MYSHOPKIT_MB_REST_NAMESPACE),
                $oAutomaticBadgeContext->getProductInfo());
        } catch (Exception $oException) {
            return MessageFactory::factory()->error(
                $oException->getMessage(),
                $oException->getCode()
            );
        }
    }
}
