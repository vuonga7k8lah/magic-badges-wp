<?php
/**
 * Định nghĩa post field tại đây.
 */

namespace MyShopKitMBWP\Product\Services\Post;



use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

trait TraitDefinePostFields {
	private array $aFields = [];

	public function defineFields(): array {
		$this->aFields = [
			'status' => [
				'key'              => 'post_status',
				'sanitizeCallback' => [ $this, 'sanitizePostStatus' ],
				'value'            => 'active',
				'assert'           => [
					'callbackFunc' => 'inArray',
					'expected'     => [ 'active', 'deactive' ]
				]
			],
			'id'     => [
				'key'              => 'ID',
				'sanitizeCallback' => 'abs',
				'value'            => 0
			],
			'slug'  => [
				'key'              => 'post_title',
				'sanitizeCallback' => 'sanitize_text_field',
				'value'            => 'magic badges',
				'assert'           => [
					'callbackFunc' => 'notEmpty'
				]
			],
			'type'   => [
				'key'        => 'post_type',
				'value'      => AutoPrefix::namePrefix('manual'),
				'isReadOnly' => true
			],
			'author' => [
				'key'        => 'post_author',
				'isRequired' => true,
				'isReadOnly' => true,
				'value'      => get_current_user_id()
			]
		];

		return $this->aFields;
	}

	public function sanitizePostStatus( $value ): string {
		return ( $value === 'active' ) ? 'publish' : 'draft';
	}
}
