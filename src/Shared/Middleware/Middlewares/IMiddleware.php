<?php


namespace MyShopKitMB\Shared\Middleware\Middlewares;


interface IMiddleware {
	public function validation(array $aAdditional= []): array;
}