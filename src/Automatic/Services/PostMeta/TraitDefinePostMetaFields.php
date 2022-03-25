<?php


namespace MyShopKitMBWP\Automatic\Services\PostMeta;


trait TraitDefinePostMetaFields {
	protected array $aFields = [];

	public function defineFields(): array {
        $this->aFields = [
            'config' => [
                'key'              => 'config',
                'assert'           => [
                    'callbackFunc' => 'isJson'
                ]
            ],
            'badgeUrl' => [
                'key'              => 'badgeUrl',
                'sanitizeCallback' => 'sanitize_text_field',
                'assert'           => [
                    'callbackFunc' => 'notEmpty'
                ]
            ]
        ];

		return $this->aFields;
	}
}
