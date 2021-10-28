<?php

namespace MyShopKitMBWP\DefaultBadge\Services\Post;



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
			'title'  => [
				'key'              => 'post_title',
				'sanitizeCallback' => 'sanitize_text_field',
				'value'            => uniqid('default_badge_'),
				'assert'           => [
					'callbackFunc' => 'notEmpty'
				]
			],
			'postType'   => [
				'key'        => 'post_type',
                'isRequired' => true,
				'value'      => '',
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
