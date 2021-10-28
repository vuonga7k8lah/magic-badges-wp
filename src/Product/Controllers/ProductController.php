<?php

namespace MyShopKitMBWP\Product\Controllers;

use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Product\Models\ManualModels;
use MyShopKitMBWP\Product\Services\Post\CreatePostService;
use MyShopKitMBWP\Product\Services\Post\DeletePostService;
use MyShopKitMBWP\Product\Services\Post\ProductQueryService;
use MyShopKitMBWP\Product\Services\Post\UpdatePostService;
use MyShopKitMBWP\Product\Services\PostMeta\AddPostMetaService;
use MyShopKitMBWP\Product\Services\PostMeta\UpdatePostMetaService;
use MyShopKitMBWP\Shared\Post\Query\PostSkeleton;
use MyShopKitMBWP\Shared\Product\ProductFactory;
use WP_REST_Request;


class ProductController
{
    protected int    $page     = 1;
    protected string $postType = '';
    protected int    $maxPages = 1;

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRouters']);
        add_filter(MYSHOPKIT_MB_WP_HOOK_PREFIX .
            'Filter/Product/Controllers/ProductController/getManualBadgesWithSlugs',
            [$this, 'handleFilterManualBadges'], 10, 3);
        add_filter(MYSHOPKIT_MB_WP_HOOK_PREFIX . 'Filter/Product/Controllers/ProductController/getAllManualBadges',
            [$this, 'handleFilterGetAllManualBadges']);
        $this->postType = (include plugin_dir_path(__FILE__) . '../Configs/PostType.php')['postType'];
    }

    public function registerRouters()
    {
        register_rest_route(MYSHOPKIT_MB_WP_REST, 'manual-products',
            [
                [
                    'methods'             => 'POST',
                    'callback'            => [$this, 'createManualProducts'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'PUT',
                    'callback'            => [$this, 'updateManualProducts'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getManualProducts'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'DELETE',
                    'callback'            => [$this, 'deleteManualProducts'],
                    'permission_callback' => '__return_true'
                ],
            ]
        );

        register_rest_route(MYSHOPKIT_MB_WP_REST, 'manual-products/(?P<id>(\d+))',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getProduct'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'PUT',
                    'callback'            => [$this, 'updateManualProduct'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'PATCH',
                    'callback'            => [$this, 'updateManualProduct'],
                    'permission_callback' => '__return_true'
                ],
                [
                    'methods'             => 'DELETE',
                    'callback'            => [$this, 'deleteManualProduct'],
                    'permission_callback' => '__return_true'
                ],
            ]
        );

        register_rest_route(MYSHOPKIT_MB_WP_REST, 'full-products',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getFullProducts'],
                    'permission_callback' => '__return_true'
                ]
            ]
        );
    }

    public function handleFilterManualBadges(array $aProduct, array $aSlugs, string $shopName): array
    {
        return $this->getManualBadges($aSlugs, $shopName);
    }

    public function getManualBadges(array $aSlugs, string $shopName): array
    {
        $aProduct = [];
        $aResponse = ManualModels::getManualBySlugs($aSlugs, $this->postType);
        if (!empty($aResponse)) {
            foreach ($aResponse as $oManual) {
                $aProduct[AutoPrefix::removeNameProduct($shopName, get_the_title($oManual->ID))] = [
                    'config'   => json_decode(get_post_meta($oManual->ID, AutoPrefix::namePrefix('config'), true),
                        true),
                    'urlImage' => get_the_post_thumbnail_url(get_post_meta($oManual->ID,
                        AutoPrefix::namePrefix('badge_id'), true))
                ];
            }
        }
        return $aProduct;
    }

    public function handleFilterGetAllManualBadges(int $countManual): int
    {
        $aResponse = $this->getCountManualBadges();
        if ($aResponse['status'] == 'success') {
            $countManual = $aResponse['data']['countPosts'];
        }
        return $countManual;
    }

    public function getCountManualBadges()
    {
        return (new ProductQueryService())
            ->setRawArgs([
                'postType' => $this->postType,
                'limit'    => 200
            ])
            ->parseArgs()
            ->setConfigKeyTitle(true)
            ->setCountItems(true)
            ->query(new PostSkeleton(), 'id,title');
    }

    public function createManualProducts(WP_REST_Request $oRequest)
    {
        $aListOfSuccess = [];
        try {
            $slugs = $oRequest->get_param('slugs');
            $productIDs = $oRequest->get_param('productIDs');
            $badgeID = $oRequest->get_param('badgeID');
            $config = $oRequest->get_param('config');
            if (empty(get_current_user_id())) {
                throw new Exception(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            if (empty($slugs)) {
                throw new Exception(esc_html__('The slugs is required',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            if (empty($productIDs)) {
                throw new Exception(esc_html__('The param productIDs is required',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $aFormatDate = $this->formatAndConvertParamsToArray($productIDs, $slugs);

            foreach ($aFormatDate as $productID => $slug) {

                $aCreateResponse = $this->handleCreateManualProduct($slug, $productID, $badgeID, $config);
                if ($aCreateResponse['status'] === 'error') {
                    $aListOfErrors[$slug] = $aCreateResponse['data'];
                } else {
                    $aListOfSuccess[$slug] = $aCreateResponse['data'];
                }
            }

            if (empty($aListOfErrors)) {
                return MessageFactory::factory('rest')->success(
                    esc_html__('Congrats, The manual have been created.', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                    [
                        'items' => array_values($aListOfSuccess)
                    ]
                );
            }

            if (count($aListOfErrors) == count($aFormatDate)) {
                return MessageFactory::factory('rest')
                    ->error(
                        sprintf(
                            esc_html__('We could not create the following product slugs: %s',
                                MYSHOPKIT_MB_WP_REST_NAMESPACE),
                            implode(",", array_keys($aListOfErrors))
                        ),
                        401
                        , [
                            'items' => array_values($aListOfErrors)
                        ]
                    );
            }

            return MessageFactory::factory('rest')
                ->success(
                    sprintf(
                        esc_html__('The following product slugs have been created: %s. We could not create the following product slugs: %s',
                            MYSHOPKIT_MB_WP_REST_NAMESPACE),
                        implode(',', array_keys($aListOfSuccess)), implode(',', array_keys($aListOfErrors))
                    ),
                    [
                        'items' => array_merge(array_values($aListOfSuccess), array_values($aListOfErrors))
                    ]
                );
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function formatAndConvertParamsToArray(...$aParam)
    {
        $aData = [];
        if (count($aParam) == 2) {
            foreach ($aParam as $key => $items) {
                $aRawSlugs = array_map(function ($slug) {
                    return trim($slug);
                }, explode(',', $items));
                $aData[$key] = array_unique($aRawSlugs);
            }
        }
        return array_combine($aData[0], $aData[1]);
    }

    public function handleCreateManualProduct(string $slug, int $productID, string $badgeID, string $config)
    {
        try {
            if (ManualModels::isCheckPostExitsByPostName($slug, $this->postType)) {
                throw new Exception(esc_html__('Sorry,The badge is already exist',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $aPostResponse = (new CreatePostService())->setRawData([
                'slug' => $slug
            ])->performSaveData();

            if ($aPostResponse['status'] == 'error') {
                throw new Exception($aPostResponse['message'], $aPostResponse['code']);
            }
            $aResponse = (new AddPostMetaService())->setID($aPostResponse['data']['id'])
                ->addPostMeta([
                    'badge_id'   => $badgeID,
                    'config'     => $config,
                    'product_id' => $productID,
                ]);

            if ($aResponse['status'] == 'error') {
                throw new Exception($aResponse['message'], $aResponse['code']);
            }

            return MessageFactory::factory()->success($aPostResponse['message'],
                [
                    'id'   => (string)$aPostResponse['data']['id'],
                    'slug' => $slug,
                    'date' => (string)strtotime(get_the_date('Y-m-d H:i:s', $aPostResponse['data']['id']))
                ]
            );
        } catch (Exception $exception) {
            return MessageFactory::factory()->error($exception->getMessage(), $exception->getCode(), [
                'id'   => '',
                'slug' => $slug,
                'date' => '',
            ]);
        }
    }

    public function updateManualProducts(WP_REST_Request $oRequest)
    {
        $aListOfSuccess = [];
        try {
            $slugs = $oRequest->get_param('slugs');
            $productIDs = $oRequest->get_param('productIDs');
            $ids = $oRequest->get_param('ids');
            $badgeID = $oRequest->get_param('badgeID');
            $config = $oRequest->get_param('config');
            if (empty(get_current_user_id())) {
                throw new Exception(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_REST_NAMESPACE), 401);
            }
            if (empty($slugs)) {
                throw new Exception(esc_html__('The slugs is required',
                    MYSHOPKIT_MB_REST_NAMESPACE), 401);
            }
            if (empty($productIDs)) {
                throw new Exception(esc_html__('The param productIDs is required',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $aFormatDate = $this->formatAndConvertParamsToArray($productIDs, $slugs);
            $aPostIDs = array_map(function ($id) {
                return (int)trim($id);
            }, explode(',', $ids));
            $i = 0;
            foreach ($aFormatDate as $productID => $slug) {
                $aUpdateResponse = $this->handleUpdateManualProduct($aPostIDs[$i], $slug, $productID, $badgeID,
                    $config);
                if ($aUpdateResponse['status'] === 'error') {
                    $aListOfErrors[$slug] = $aUpdateResponse['data'];
                } else {
                    $aListOfSuccess[$slug] = $aUpdateResponse['data'];
                }
                $i++;
            }

            if (empty($aListOfErrors)) {
                return MessageFactory::factory('rest')->success(
                    esc_html__('Congrats, The manual have been updated.', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                    [
                        'items' => array_values($aListOfSuccess)
                    ]
                );
            }

            if (count($aListOfErrors) == count($aFormatDate)) {
                return MessageFactory::factory('rest')
                    ->error(
                        sprintf(
                            esc_html__('We could not update the following product slugs: %s',
                                MYSHOPKIT_MB_WP_REST_NAMESPACE),
                            implode(",", array_keys($aListOfErrors))
                        ),
                        401
                        , [
                            'items' => array_values($aListOfErrors)
                        ]
                    );
            }

            return MessageFactory::factory('rest')
                ->success(
                    sprintf(
                        esc_html__('The following product slugs have been updated: %s. We could not update the following product slugs: %s',
                            MYSHOPKIT_MB_WP_REST_NAMESPACE),
                        implode(',', array_keys($aListOfSuccess)), implode(',', array_keys($aListOfErrors))
                    ),
                    [
                        'items' => array_merge(array_values($aListOfSuccess), array_values($aListOfErrors))
                    ]
                );
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function handleUpdateManualProduct(
        int $postID,
        string $slug,
        string $productID,
        string $badgeID,
        string $config
    ) {
        try {
            $aPostResponse = (new UpdatePostService())
                ->setID($postID)
                ->setRawData([
                    'slug' => $slug
                ])
                ->performSaveData();

            if ($aPostResponse['status'] == 'error') {
                return MessageFactory::factory('rest')->error($aPostResponse['message'], $aPostResponse['code']);
            }

            if ($aPostResponse['status'] == 'success') {
                $aResponse = (new UpdatePostMetaService())
                    ->setID($aPostResponse['data']['id'])
                    ->updatePostMeta([
                        'badge_id'   => $badgeID,
                        'product_id' => $productID,
                        'config'     => $config,
                    ]);

                if ($aResponse['status'] == 'error') {
                    return MessageFactory::factory('rest')->error($aResponse['message'], $aResponse['code']);
                }
            }
            return MessageFactory::factory()->success($aPostResponse['message'],
                [
                    'id'   => (string)$aPostResponse['data']['id'],
                    'slug' => $slug,
                    'date' => (string)strtotime(get_the_modified_date('Y-m-d H:i:s', $aPostResponse['data']['id']))
                ]
            );
        } catch (Exception $exception) {
            return MessageFactory::factory()->error($exception->getMessage(), $exception->getCode(), [
                'id'   => '',
                'slug' => $slug,
                'date' => '',
            ]);
        }
    }

    public function updateManualProduct(WP_REST_Request $oRequest)
    {
        $postID = (int)$oRequest->get_param('id');
        $slug = (string)$oRequest->get_param('slug');
        $badgeID = $oRequest->get_param('badgeID');
        $config = $oRequest->get_param('config');
        $productID = $oRequest->get_param('productID');
        if (empty(get_current_user_id())) {
            return MessageFactory::factory('rest')
                ->error(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
        }
        $aResponse = $this->handleUpdateManualProduct($postID, $slug, $productID, $badgeID, $config);
        if ($aResponse['status'] == 'error') {
            return MessageFactory::factory('rest')
                ->error($aResponse['message'], $aResponse['code'], $aResponse['data']);
        }

        return MessageFactory::factory('rest')
            ->success($aResponse['message'], $aResponse['data']);
    }

    public function deleteManualProduct(WP_REST_Request $oRequest)
    {
        $postID = (int)$oRequest->get_param('id');
        $aPostResponse = (new DeletePostService())->setID($postID)->delete();
        if ($aPostResponse['status'] == 'error') {
            return MessageFactory::factory('rest')->error($aPostResponse['message'], $aPostResponse['code']);
        }

        return MessageFactory::factory('rest')->success(
            $aPostResponse['message'],
            [
                'id' => $aPostResponse['data']['id']
            ]
        );
    }

    public function deleteManualProducts(WP_REST_Request $oRequest)
    {
        $aPostIDs = explode(',', $oRequest->get_param('ids'));

        if (empty($aPostIDs)) {
            return MessageFactory::factory('rest')->error(
                esc_html__('Please provide 1 manual product at least', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                400
            );
        }

        $aListOfErrors = [];
        $aListOfSuccess = [];
        $oDeletePostServices = new DeletePostService();

        foreach ($aPostIDs as $postID) {
            $aDeleteResponse = $oDeletePostServices->setID($postID)->delete();
            if ($aDeleteResponse['status'] === 'error') {
                $aListOfErrors[] = $postID;
            } else {
                $aListOfSuccess[] = $postID;
            }
        }

        if (empty($aListOfErrors)) {
            return MessageFactory::factory('rest')->success(
                esc_html__('Congrats, the manual product have been deleted.', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                [
                    'id' => implode(',', $aListOfSuccess)
                ]
            );
        }

        if (count($aListOfErrors) == count($aPostIDs)) {
            return MessageFactory::factory('rest')
                ->error(
                    sprintf(
                        esc_html__('We could not delete the following popup ids: %s',
                            MYSHOPKIT_MB_WP_REST_NAMESPACE),
                        implode(",", $aListOfErrors)
                    ),
                    401
                );
        }

        return MessageFactory::factory('rest')
            ->success(
                sprintf(
                    esc_html__('The following ids have been deleted: %s. We could not delete the following ids: %s',
                        MYSHOPKIT_MB_WP_REST_NAMESPACE),
                    implode(',', $aListOfSuccess), implode(',', $aListOfErrors)
                )
            );
    }

    /**
     * @throws Exception
     */
    public function getManualProducts(WP_REST_Request $oRequest)
    {
        $aProducts = [];
        try {
            if (!empty($oRequest->get_param('s'))) {
                $oRequest->set_param('s', sanitize_title($oRequest->get_param('s')));
                $search = $oRequest->get_param('s');
            }
            if (!get_current_user_id()) {
                throw new Exception(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }
            $aArgs = [
                'postType' => $this->postType,
                'limit'    => 200
            ];
            if (!empty($search)) {
                $aArgs['s'] = $search;
            }
            $aManualResponse = (new ProductQueryService())->setRawArgs($aArgs)->parseArgs()->setConfigKeyTitle(true)
                ->query(new PostSkeleton(), 'id,title');
            if ($aManualResponse['status'] === 'error') {
                return MessageFactory::factory('rest')->error(
                    esc_html__('Sorry, We could not find your product', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                    $aManualResponse['code']
                );
            }
            if (!empty($aDataManualProduct = $aManualResponse['data']['items'])) {

                $aProductIDs = array_keys($aDataManualProduct);
                $aResponseWC = (ProductFactory::setPlatform('woocommerce'))->getProductsByProductID($aProductIDs,
                    get_current_user_id(),
                    $oRequest->get_params());

                if ($aResponseWC['status'] == 'error') {
                    throw new Exception($aResponseWC['message'], 401);
                }
                foreach ($aResponseWC['data']['items'] as $aItem) {
                    if (isset($aDataManualProduct[$aItem['id']])) {
                        $aDataManual = $aDataManualProduct[$aItem['id']];
                        $aProducts[] = array_merge($aItem, [
                            'manual'     => [
                                'config'   => json_decode($aDataManual['config'], true),
                                'urlImage' => $aDataManual['urlImage'],
                                'badge_id' => $aDataManual['badgeID'],
                                'id'       => $aDataManual['id'],
                            ],
                            'isSelected' => false,
                        ]);
                    }
                }
                return MessageFactory::factory('rest')->success(sprintf(esc_html__('We found %s products',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), count($aProducts)), [
                    'items'    => $aProducts,
                    'maxPages' => $aResponseWC['data']['maxPages']
                ]);
            }
            return MessageFactory::factory('rest')->success(esc_html__('We not found product',
                MYSHOPKIT_MB_WP_REST_NAMESPACE), [
                'items'   => $aProducts,
                'maxPage' => 1
            ]);
        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function getFullProducts(WP_REST_Request $oRequest)
    {
        $aProducts = [];
        $search = $oRequest->get_param('s');
        $limit = $oRequest->get_param('limit') ?? 50;
        try {
            if (empty($customerID = get_current_user_id())) {
                throw new Exception(esc_html__('You must be logged in before performing this function',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), 401);
            }

            //lấy tối đa 200 sp đã lưu ra để kiểm tra
            $aManualResponse = (new ProductQueryService())->setRawArgs(
                array_merge(
                    $oRequest->get_params(),
                    [
                        'postType' => $this->postType,
                        'limit'    => 200
                    ]
                )
            )->parseArgs()->setConfigKeyTitle(true)->query(new PostSkeleton(), 'id');
            if ($aManualResponse['status'] === 'error') {
                return MessageFactory::factory('rest')->error(
                    esc_html__('Sorry, We could not find your product', MYSHOPKIT_MB_WP_REST_NAMESPACE),
                    $aManualResponse['code']
                );
            }
            $aDataManualProduct = $aManualResponse['data']['items'];
            $page =(int) $oRequest->get_param('page') ?? $this->page;
            $i = 0;
            do {
                $aWCResponse = $this->getProductsWC($customerID,
                    [
                        'limit' => $limit,
                        'page'  => $page +$i
                    ]);
                $this->maxPages = $aWCResponse['data']['maxPages'];
                if ($aWCResponse['status'] == 'error') {
                    throw new Exception($aWCResponse['message'], $aWCResponse['code']);
                }
                $aWCProduct = $aWCResponse['data']['items'];
                $this->handleFilterProduct(
                    $aWCProduct,
                    $aDataManualProduct,
                    $aProducts,
                    20);
                $i++;
            } while (!(count($aProducts) == 20) && !($i == 5));

            return MessageFactory::factory('rest')->success(sprintf(esc_html__('We found %s products',
                MYSHOPKIT_MB_WP_REST_NAMESPACE), count($aProducts)), [
                'items'    => array_values($aProducts),
                'maxPages' => $this->maxPages
            ]);

        } catch (Exception $exception) {
            return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws Exception
     */
    public function getProductsWC(string $customerID, array $aParams): array
    {
        return (ProductFactory::setPlatform('woocommerce'))->getProducts($customerID,
            $aParams);
    }

    public function handleFilterProduct(
        array $aWCProduct,
        array &$aDataManualProduct,
        array &$aProducts,
        int $limit
    ): array {

        foreach ($aWCProduct as $aItem) {

            //nếu số lượng sp bằng 50 hoặc bằng số lượng limit thì thoát khỏi vòng lặp
            if (count($aProducts) == 50 || count($aProducts) == $limit) {
                break;
            }

            $isProductExist = isset($aDataManualProduct[$aItem['id']]);
            if (!$isProductExist) {
                $aProducts[] = array_merge($aItem, [
                    'manual'     => [],
                    'isSelected' => false,
                ]);
            } else {
                unset($aDataManualProduct[$aItem['id']]);
            }

            //$this->page = $aItem['cursor'];
        }

        return $aProducts;
    }
}
