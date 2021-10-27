<?php


namespace WilokeJWTSDK;


trait TraitDefine {
	private static ?WilokeJWTSDK $oSelf        = null;
	private string               $namespace    = 'wiloke-jwt';
	private array                $aDefaultAPI
	                                           = [
			'redirect_uri'   => '',
			'app_id'         => '',
			'app_secret'     => '',
			'client_session' => ''
		];
	private array                $aAPI         = [];
	private string               $version      = '';
	private string               $rootEndpoint = 'https://creatior.myshopkit.app/wp-json/wiloke-jwt';
	private string               $ds           = '/';
}
