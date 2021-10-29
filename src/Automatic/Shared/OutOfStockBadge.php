<?php

namespace MyShopKitMB\Automatic\Shared;

use Exception;
use MyShopKitMB\Illuminate\Message\MessageFactory;
use MyShopKitMB\Illuminate\Prefix\AutoPrefix;

class OutOfStockBadge implements IAutomaticState
{
    use TraitSetAutomaticBadges;
    use TraitDetectNextAutomaticBadge;

    private static ?array $aSettings
        = [
            'setting' => 'out_of_stock'
        ];

    public function __construct(AutomaticContext $oAutomaticContext)
    {
        $this->oAutomaticContext = $oAutomaticContext;
    }

    /**
     * @throws Exception
     */
    public function proceedToNextBadge(): IAutomaticState
    {
        $this->oAutomaticContext->setState($this->getNextAutomaticBadge($this));

        return $this;
    }

    public function isAppliedBadge(): bool
    {
        $aResponse = $this->response();

        return $aResponse['status'] == 'success';
    }

    public function response(): array
    {
        if (!$this->hasSetting()) {
            return MessageFactory::factory()->error(esc_html__('This product has not added to the On Sale Badge',
                'myshopkit-magic-badges'),
                400);
        }

        if (!$this->isOutOfStock()) {
            return MessageFactory::factory()->error(esc_html__('This product is not on sale', 'myshopkit-magic-badges'),
                400);
        }

        $this->oAutomaticContext->applyProductBadge(self::$aSettings);

        return MessageFactory::factory()->success(esc_html__('Applied Badge', 'myshopkit-magic-badges'));
    }

    private function hasSetting(): bool
    {
        if (self::$aSettings !== null) {
            $aPosts = get_posts([
                'post_type'      => AutoPrefix::namePrefix('out_of_stock'),
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'author'         => $this->oAutomaticContext->getUserID()
            ]);
        }

        if (empty($aPosts)) {
            self::$aSettings = [];
            return false;
        }
        $jConfig=get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('config'), true);
        self::$aSettings = [
            'config'   => json_decode($jConfig,true),
            'urlImage' => get_the_post_thumbnail_url(get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('badge_id'),
                true))
        ];

        return true;
    }

    /**
     * https://www.dropbox.com/s/9raedhd44b7wxge/Screen%20Shot%202021-09-17%20at%2012.09.58.png?dl=0
     * @return false
     */
    private function isOutOfStock(): bool
    {
        return $this->oAutomaticContext->getProductInfo()['hasOutOfStockVariants'];
    }
}
