<?php


namespace MyShopKitMBWP\Automatic\Services\PostMeta;





use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Shared\Post\IDeleteUpdateService;
use MyShopKitMBWP\Shared\Post\IService;
use MyShopKitMBWP\Shared\Post\TraitIsPostAuthor;
use MyShopKitMBWP\Shared\Post\TraitMaybeAssertion;
use MyShopKitMBWP\Shared\Post\TraitMaybeSanitizeCallback;

class PostMetaService implements IService, IDeleteUpdateService {
	protected array $aRawData = [];
	protected array $aData    = [];
	protected       $postID;
	private bool    $isUpdate = false;

	use TraitDefinePostMetaFields;
	use TraitMaybeAssertion;
	use TraitMaybeSanitizeCallback;
	use TraitIsPostAuthor;

	public function setID( $id ): self {
		$this->postID = $id;

		return $this;
	}

	public function setRawData( array $aRawData ): IService {
		$this->aRawData = $aRawData;

		return $this;
	}

	public function performSaveData(): array {
		try {
			$this->validateFields();
			foreach ( $this->aData as $metaKey => $metaValue ) {
				update_post_meta( $this->postID, AutoPrefix::namePrefix( $metaKey ), $metaValue );
			}

			return MessageFactory::factory()->success(
				esc_html__( 'The data have been updated successfully.', MYSHOPKIT_MB_WP_REST_NAMESPACE )
			);
		}
		catch ( \Exception $oException ) {
			return MessageFactory::factory()->error( $oException->getMessage(), $oException->getCode() );
		}
	}

	protected function setIsUpdate( $status ): PostMetaService {
		$this->isUpdate = $status;

		return $this;
	}

	/**
	 * @throws \Exception
	 */
	public function validateFields(): IService {
		if ( ! $this->postID ) {
			throw new \Exception( esc_html__( 'You must set the post id.', MYSHOPKIT_MB_WP_REST_NAMESPACE ) );
		}

		$this->isPostAuthor( $this->postID );
		foreach ( $this->defineFields() as $friendlyKey => $aField ) {
			if ( isset( $aField['isReadOnly'] ) ) {
				if ( ! $this->isUpdate ) {
					$this->aData[ $aField['key'] ] = $aField['value'];
				}
			} else {
				if ( $this->isUpdate ) {
					if ( ! isset( $this->aRawData[ $friendlyKey ] ) ) {
						continue;
					}
				}

				$value = $this->aRawData[ $friendlyKey ] ?? '';

				// Kiem tha du lieu co dung voi format
				$aAssertionResponse = $this->maybeAssert( $aField, $value );
				if ( $aAssertionResponse['status'] === 'error' ) {
					throw new \Exception( $aAssertionResponse['message'] );
				}

				$this->aData[ $aField['key'] ] = $this->maybeSanitizeCallback( $aField, $value );
			}
		}

		return $this;
	}
}
