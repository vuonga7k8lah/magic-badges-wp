<?php


namespace MyShopKitMBWP\Product\Services\Post;



use Exception;
use MyShopKitMBWP\DefaultBadge\Services\Post\PostService;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Shared\Post\IDeleteUpdateService;
use MyShopKitMBWP\Shared\Post\IService;
use MyShopKitMBWP\Shared\Post\TraitIsPostAuthor;
use MyShopKitMBWP\Shared\Post\TraitMaybeAssertion;
use MyShopKitMBWP\Shared\Post\TraitMaybeSanitizeCallback;


class UpdatePostService extends PostService implements IService, IDeleteUpdateService {
	use TraitDefinePostFields;
	use TraitMaybeAssertion;
	use TraitMaybeSanitizeCallback;
	use TraitIsPostAuthor;

	private $postID;

	public function setID( $id ): self
    {
        $this->postID = $id;

        return $this;
    }

	/**
	 * @throws Exception
	 */
	public function validateFields(): IService {
		if ( empty( $this->postID ) ) {
			throw new \Exception( esc_html__( 'The ID is required.', MYSHOPKIT_MB_REST_NAMESPACE ) );
		}

		$this->isPostAuthor( $this->postID );
		foreach ( $this->defineFields() as $friendlyKey => $aField ) {
			if ( isset( $aField['isReadOnly'] ) || ! isset( $this->aRawData[ $friendlyKey ] ) ||
			     ! isset( $this->aRawData[ $friendlyKey ] ) ) {
				continue;
			} else {
				$value = $this->aRawData[ $friendlyKey ];
				// Kiem tha du lieu co dung voi format
				$aAssertionResponse = $this->maybeAssert( $aField, $value );
				if ( $aAssertionResponse['status'] === 'error' ) {
					throw new \Exception( $aAssertionResponse['message'] );
				}

				$this->aData[ $aField['key'] ] = $this->maybeSanitizeCallback( $aField, $value );
			}
		}

		$this->aData['ID'] = $this->postID;

		return $this;
	}

	public function performSaveData(): array {
		try {
		    $this->validateFields();
			$id = wp_update_post( $this->aData );
			if ( is_wp_error( $id ) ) {
				return MessageFactory::factory()->error( $id->get_error_message(), $id->get_error_code() );
			}

			return MessageFactory::factory()->success(
				esc_html__( 'Congrats! The product has been updated successfully.', MYSHOPKIT_MB_WP_REST_NAMESPACE ),
				[
					'id' => $id
				]
			);
		}
		catch ( \Exception $oException ) {
			return MessageFactory::factory()->error( $oException->getMessage(), $oException->getCode() );
		}
	}

	/**
	 * @param array $aRawData
	 *
	 * @return IService
	 */
	public function setRawData( array $aRawData ): IService {
		$this->aRawData = $aRawData;

		return $this;
	}
}
