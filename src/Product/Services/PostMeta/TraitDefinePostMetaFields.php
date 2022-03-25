<?php


namespace MyShopKitMBWP\Product\Services\PostMeta;


trait TraitDefinePostMetaFields
{
    protected array $aFields = [];

    public function defineFields(): array
    {
        $this->aFields = [
            'config'     => [
                'key'    => 'config',
                'assert' => [
                    'callbackFunc' => 'isJson'
                ]
            ],
            'badge_url'   => [
                'key'              => 'badge_url',
                'sanitizeCallback' => 'sanitize_text_field',
                'assert'           => [
                    'callbackFunc' => 'notEmpty'
                ]
            ],
            'product_id' => [
                'key'              => 'product_id',
                'sanitizeCallback' => 'sanitize_text_field',
                'assert'           => [
                    'callbackFunc' => 'notEmpty'
                ]
            ]
        ];

        return $this->aFields;
    }
}
