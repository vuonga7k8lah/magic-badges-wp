<?php


namespace WilokeJWTSDK;


class WilokeJWTSDK {
	use TraitDefine;

	/**
	 * @param array{redirect_ur?: string, app_id: string, app_secret: string, client_session: string  } $aAPI
	 * @param string $version
	 *
	 * @return WilokeJWTSDK|null
	 */
	public static function setup( array $aAPI, string $version = 'v1' ): ?WilokeJWTSDK {
		if ( ! self::$oSelf ) {
			self::$oSelf = new self();
		}

		self::$oSelf->performSetup( $aAPI, $version );

		return self::$oSelf;
	}

	private function performSetup( array $aAPI, string $version ): WilokeJWTSDK {
		$this->aAPI    = wp_parse_args( $aAPI, $this->aDefaultAPI );
		$this->version = $version;

		return $this;
	}

	private function generateEndpoint( string $namespace ): string {
		return $this->rootEndpoint . $this->ds . $this->version . $this->ds . $namespace;
	}

	private function getBody( $response ): array {
		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return [];
		}


		$aResponse = json_decode( $body, true );
		if ( empty( $aResponse ) ) {
			$aResponse = json_decode( stripslashes( $body ), true );
		}

		return $aResponse;
	}

	public function requestAuthCode(): array {
		$response = wp_remote_post(
			$this->generateEndpoint( 'auth-code' ),
			[
				'body' => $this->aAPI
			]
		);

		if ( empty( $response ) || is_wp_error( $response ) ) {
			return [
				'status'  => 'error',
				'code'    => 400,
				'message' => 'Something went error'
			];
		}

		return $this->getBody( $response );
	}

	/**
	 * @param string $code Tra lai tu api requestAuthCode
	 * @param string $email
	 *
	 * @return string[]
	 */
	public function signUp( string $code, string $email ): array {
		$response = wp_remote_post(
			$this->generateEndpoint( 'sign-up' ),
			[
				'body' => array_merge( $this->aAPI, [ 'email' => $email, 'code' => $code ] )
			]
		);

		if ( empty( $response ) || is_wp_error( $response ) ) {
			return [
				'status'  => 'error',
				'code'    => 400,
				'message' => 'Something went error'
			];
		}


		$aResponse = $this->getBody( $response );
		if ( ! is_array( $aResponse ) ) {
			return [
				'status'  => 'error',
				'code'    => 400,
				'message' => 'We could not parse signUp response json'
			];
		}

		return $aResponse;
	}

	public function renewToken( string $accessToken, string $refreshToken ): array {
		$response = wp_remote_post(
			$this->generateEndpoint( 'renew-token' ),
			[
				'body' => array_merge( $this->aAPI, [ 'accessToken' => $accessToken, 'refreshToken' => $refreshToken ] )
			]
		);

		if ( ! $aResponse = $this->getBody( $response ) ) {
			return [
				'status'  => 'error',
				'code'    => 400,
				'message' => 'Something went error'
			];
		}

		return $aResponse;
	}

	public function validateToken( ?string $accessToken ): array {
		$response = wp_remote_post(
			$this->generateEndpoint( 'token-validation' ),
			[
				'body' => array_merge( $this->aAPI, [ 'accessToken' => $accessToken ] )
			]
		);

		if ( ! $aResponse = $this->getBody( $response ) ) {
			return [
				'status'  => 'error',
				'code'    => 400,
				'message' => 'Something went error'
			];
		}

		return $aResponse;
	}
}
