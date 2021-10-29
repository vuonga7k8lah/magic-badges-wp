<?php


namespace MyShopKitMBWP\Shared\Middleware\Middlewares;


use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;


class IsBadgeExistMiddleware implements IMiddleware
{
    protected array $aStatusBadge = ['publish', 'draft'];

    /**
     * @throws Exception
     */
    public function validation(array $aAdditional = []): array
    {
        $postID = $aAdditional['postID'] ?? '';
        if (empty($postID)) {
            throw new Exception('Sorry, the Badge is required', 400);
        }
        if (!in_array(get_post_status($postID), $this->aStatusBadge)) {
            throw new Exception('Sorry, the post doest not exist at the moment', 400);
        }

        return MessageFactory::factory()->success('Passed');
    }
}
