<?php

namespace MyShopKitMBWP\Automatic\Shared;

use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class OnSaleBadge implements IAutomaticState
{
	use TraitSetAutomaticBadges;
	use TraitDetectNextAutomaticBadge;

	private static ?array $aSettings
		= [
			'setting' => 'on_sale_badge'
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
			return MessageFactory::factory()->error(
				esc_html__('This product has not added to the On Sale Badge', 'myshopkit-magic-badges'),
				400
			);
		}
		if (!$this->isOnSale()) {
			return MessageFactory::factory()->error(
				esc_html__('This product is not on sale', 'myshopkit-magic-badges'),
				400
			);
		}

		$this->oAutomaticContext->applyProductBadge(self::$aSettings);

		return MessageFactory::factory()->success(esc_html__('Applied Badge', 'myshopkit-magic-badges'));
	}

	private function hasSetting(): bool
	{
		if (self::$aSettings !== null) {
			$aPosts = get_posts([
				'post_type'      => AutoPrefix::namePrefix('on_sale'),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'author'         => $this->oAutomaticContext->getUserID()
			]);
		}
		if (empty($aPosts)) {
			self::$aSettings = [];
			return false;
		}

		$jConfig = get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('config'), true);
		self::$aSettings = [
			'config'   => json_decode($jConfig, true),
			'urlImage' => get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('badgeUrl'), true)
		];
		return true;
	}

	/**
	 * https://www.dropbox.com/s/o9julyli0bcftkl/Screen%20Shot%202021-09-17%20at%2012.01.15.png?dl=0
	 *
	 * @return false
	 */
	private function isOnSale(): bool
	{
//		$maxPrice = $this->oAutomaticContext->getProductInfo()['priceRangeV2']['maxVariantPrice']['amount'];
//		$minPrice = $this->oAutomaticContext->getProductInfo()['priceRangeV2']['minVariantPrice']['amount'];

		return $this->oAutomaticContext->getProductInfo()['onSale'];;
	}
}
