<?php


namespace MyShopKitMB\Shared\Middleware\Middlewares;




use MyShopKitMB\Illuminate\Message\MessageFactory;

class IsShopLoggedInLowLevelCheckMiddleware implements IMiddleware {

	public function validation( array $aAdditional = [] ): array {
		if ( ! ebaseGetCurrentShopID( false ) ) {
			return MessageFactory::factory()->error(
			esc_html__('Sorry, We could not find your shop'),400
			);
		}

		return MessageFactory::factory()->success( 'Passed' );
	}
}
