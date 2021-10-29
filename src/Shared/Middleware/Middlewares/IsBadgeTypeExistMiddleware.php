<?php


namespace MyShopKitMBWP\Shared\Middleware\Middlewares;


use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Shared\Post\TraitPostHelps;


class IsBadgeTypeExistMiddleware implements IMiddleware
{
    use TraitPostHelps;

    /**
     * @throws Exception
     */
    public function validation(array $aAdditional = []): array
    {
        $postType = $aAdditional['postType'] ?? '';
        if (empty($postType)) {
            throw new Exception('Sorry, the param postType is require', 400);
        }
        if (!in_array($postType, $this->getPostTypes())) {
            throw new Exception('Sorry, the item is no longer available', 400);
        }
        return MessageFactory::factory()->success('Passed');
    }
}
