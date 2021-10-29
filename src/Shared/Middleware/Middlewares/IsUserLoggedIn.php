<?php


namespace MyShopKitMBWP\Shared\Middleware\Middlewares;


use MyShopKitMBWP\Illuminate\Message\MessageFactory;

class IsUserLoggedIn implements IMiddleware
{

    public function validation(array $aAdditional = []): array
    {
        if (!is_user_logged_in()) {
            return MessageFactory::factory()->error(
                esc_html__('Sorry, You must log into the App to use this feature', MYSHOPKIT_MB_REST_NAMESPACE),
                400
            );
        }

        return MessageFactory::factory()->success('Passed');
    }
}
