<?php


namespace MyShopKitMBWP\Shared\Middleware\Middlewares;


interface IMiddleware {
	public function validation(array $aAdditional= []): array;
}
