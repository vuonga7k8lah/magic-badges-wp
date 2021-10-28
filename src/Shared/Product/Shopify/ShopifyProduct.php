<?php

namespace MyShopKitMB\Shared\Product\Shopify;

use EBase\Logger\Models\LogModel;
use EBase\Shared\Slack\PostMessage;
use EBase\Shopify\LoginRegister\Models\CustomerShopModel;
use EBase\Shopify\Shared\Connection;
use Exception;
use MyShopKitMB\Illuminate\Message\MessageFactory;
use MyShopKitMB\Shared\Product\Interfaces\IPlatform;
use PHPShopify\Exception\ApiException;
use PHPShopify\Exception\CurlException;
use PHPShopify\ShopifySDK;

class ShopifyProduct implements IPlatform
{
    protected array     $aArgs           = [];
    protected array     $aProducts       = [];
    protected array     $aResponse       = [];
    protected array     $aPrettyProducts = [];
    private ?ShopifySDK $oConnect;

    public function getCost(): array
    {
        if (isset($this->aResponse['extensions'])) {
            return MessageFactory::factory()->success('Found it', $this->aResponse['extensions']['cost']);
        }

        return MessageFactory::factory()->error(esc_html__('You must process a query first',
            'myshopkit-magic-badges'), 400);
    }

    public function getLastCursor(): string
    {
        if (!empty($this->aPrettyProducts)) {
            $aItems = $this->aProducts;
            $aEnd = end($aItems);
            return $aEnd['cursor'];
        }

        return '';
    }

    public function hasNextPage(): bool
    {
        if (!empty($this->aPrettyProducts)) {
            return $this->aResponse['data']['products']['pageInfo']['hasNextPage'];
        }

        return false;
    }

    public function getProducts($customerID, array $aArgs = []): array
    {

        $aArgs = wp_parse_args($aArgs, [
            'cursor'  => '',
            'limit'   => 10,
            'orderby' => 'TITLE',
        ]);

        try {
            $graphQL = <<<Query
				query getProducts(\$quantity: Int!, \$cursor: String, \$query: String, \$orderby: ProductSortKeys) {
				products(first: \$quantity, after: \$cursor, query: \$query, sortKey: \$orderby) {
				      pageInfo {
				        hasNextPage
				      }
				      edges {
				        cursor
				        node {
				          id
				          title,
				          createdAt,
				           priceRangeV2 {
					          maxVariantPrice {
					            amount
					          }
					          minVariantPrice {
					            amount
					          }
					        },
					        featuredImage {
				            height
				            src
				            width
				            },
				            handle
				      }
				        }
				    }
				}
				Query;

            $aVariables = [
                'quantity' => (int)$aArgs['limit']
            ];

            if (!empty($aArgs['cursor'])) {
                $aVariables ['cursor'] = $aArgs['cursor'];
            }
            if (!empty($aArgs['orderby'])) {
                $aVariables ['orderby'] = strtoupper($aArgs['orderby']);
            }
            $this->aResponse = $this->connect($customerID)->GraphQL->post($graphQL, null, null, $aVariables);
            if (empty($this->aResponse['data']['products']['edges'])) {
                $hasNextPage = false;
                $this->aPrettyProducts = [];
            } else {
                $this->aProducts = $this->aResponse['data']['products']['edges'];
                $this->aPrettyProducts = $this->parseProducts($this->aProducts);
                $hasNextPage = $this->aResponse['data']['products']['pageInfo']['hasNextPage'];
            }

            return MessageFactory::factory()->success('Found it',
                [
                    'items'       => $this->aPrettyProducts,
                    'hasNextPage' => $hasNextPage
                ]);
        } catch (Exception $oException) {
            return MessageFactory::factory()->error($oException->getMessage(), $oException->getCode());
        }
    }

    private function connect($customerID): ?ShopifySDK
    {
        $shopName = CustomerShopModel::getShopifyShopNameByCustomerID($customerID);
        $shopID = CustomerShopModel::getIdByCustomerIDAndShopName($customerID, $shopName);
        $accessToken = CustomerShopModel::getPlatformToken($shopID);
        $this->oConnect = Connection::connect($shopName, $accessToken)->getShopifySDK();
        return $this->oConnect;
    }

    public function parseProducts(array $aProducts): array
    {
        $this->aPrettyProducts = array_reduce($aProducts,
            function ($aCarry, $aItem) {
                $aItem['node']['cursor'] = $aItem['cursor'];
                $aCarry[] = $aItem['node'];
                return $aCarry;
            }, []);

        return $this->aPrettyProducts;
    }

    public function search($titleKeyword, $customerID, array $aArgs = [], $isExtract = false): array
    {
        $aArgs = wp_parse_args($aArgs, [
            'cursor'  => '',
            'limit'   => 50,
            'orderby' => 'TITLE',
        ]);
        try {
            $graphQL = <<<Query
				query getProducts(\$quantity: Int!, \$cursor: String, \$query: String) {
				products(first: \$quantity, after: \$cursor, query: \$query) {
				      pageInfo {
				        hasNextPage
				      }
				      edges {
				        cursor
				        node {
				          id
				          title,
				          createdAt,
				           priceRangeV2 {
					          maxVariantPrice {
					            amount
					          }
					          minVariantPrice {
					            amount
					          }
					        },
					        featuredImage {
				            height
				            src
				            width
				            },
				            handle
				        }
				      }
				    }
				}
				Query;

            if ($isExtract) {
                $titleKeyword = 'title:"' . $titleKeyword . '"';
            } else {
                $titleKeyword = 'title:' . $titleKeyword . '*';
            }

            $aVariables = [
                'quantity' => $aArgs['limit'],
                'query'    => $titleKeyword
            ];

            if (!empty($aArgs['cursor'])) {
                $aVariables ['cursor'] = $aArgs['cursor'];
            }

            if (!empty($aArgs['orderby'])) {
                $aVariables ['orderby'] = strtoupper($aArgs['orderby']);
            }

            $this->aProducts = $this->connect($customerID)->GraphQL->post($graphQL, null, null, $aVariables);

            if (empty($this->aProducts['data']['products']['edges'])) {
                $aProducts = [];
                $hasNextPage = false;
            } else {
                $aProducts = $this->parseProducts($this->aProducts['data']['products']['edges']);
                $hasNextPage = $this->aProducts['data']['products']['pageInfo']['hasNextPage'];
            }

            return MessageFactory::factory()->success('Found it', [
                'items'       => $aProducts,
                'hasNextPage' => $hasNextPage
            ]);
        } catch (Exception $oException) {
            return MessageFactory::factory()->error($oException->getMessage(), $oException->getCode());
        }
    }

    /**
     * @throws ApiException
     * @throws CurlException
     */
    public function getProductsBySlug(array $aSlug, $customerID, array $aArgs = []): array
    {
        try {
            $aArgs = wp_parse_args($aArgs, [
                'cursor' => '',
                'limit'  => 50
            ]);
            $titles = array_reduce($aSlug, function ($carry, $title) {
                if (empty($carry)) {
                    $carry = "'" . $title . "'";
                } else {
                    $carry .= " OR '" . $title . "'";
                }
                return $carry;
            }, "");

            $graphQL = <<<Query
		 query getProducts(\$quantity: Int!, \$cursor: String, \$query: String) {
			products(first: \$quantity, after: \$cursor, query: \$query,sortKey: TITLE) {
			      pageInfo {
			        hasNextPage
			      }
			      edges {
				        cursor
				        node {
				          id
				          title,
				          createdAt,
			              priceRangeV2 {
					          maxVariantPrice {
					            amount
					          }
					          minVariantPrice {
					            amount
					          }
					        },
					        featuredImage {
					            height
					            src
					            width
				            },
				            handle,
				            hasOutOfStockVariants,
			                variants(first: 4) {
				                edges {
				                    node {
				                        compareAtPrice
				                    }
				                }
				            }
			            }
			        }
			    }
			}
		Query;

            $aVariables = [
                'quantity' => (int)$aArgs['limit'],
                'query'    => $titles
            ];
            if (!empty($aArgs['cursor'])) {
                $aVariables ['cursor'] = $aArgs['cursor'];
            }

            $this->aProducts = $this->connect($customerID)->GraphQL->post($graphQL, null, null, $aVariables);
            if (empty($this->aProducts['data']['products']['edges'])) {
                $aProducts = [];
                $hasNextPage = false;
            } else {
                $aProducts = $this->parseProducts($this->aProducts['data']['products']['edges']);
                $hasNextPage = $this->aProducts['data']['products']['pageInfo']['hasNextPage'];
            }
            return MessageFactory::factory()->success('Found it', [
                'items'       => $aProducts,
                'hasNextPage' => $hasNextPage
            ]);
        } catch (Exception $oException) {
            PostMessage::postMessage('Warning', [$oException->getMessage()], LogModel::HIGH);

            return MessageFactory::factory()->error($oException->getMessage(), $oException->getCode());
        }
    }
}
