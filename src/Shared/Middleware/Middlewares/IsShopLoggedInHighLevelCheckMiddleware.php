<?php


namespace MyShopKitMBWP\Shared\Middleware\Middlewares;


use MyShopKitMBWP\Illuminate\Message\MessageFactory;

class IsShopLoggedInHighLevelCheckMiddleware implements IMiddleware
{

    public function validation(array $aAdditional = []): array
    {
        if (!ebaseGetCurrentShopID()) {
            return MessageFactory::factory()->error(esc_html__('Sorry, We could not find your shop',
                MYSHOPKIT_MB_REST_NAMESPACE), 400);
        }

        return MessageFactory::factory()->success('Passed');
    }
}
