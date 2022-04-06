<?php

namespace MyShopKitMBWP\Automatic\Shared;

use DateTime;
use DateTimeZone;
use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class NewArrivalBadge implements IAutomaticState
{
	use TraitSetAutomaticBadges, TraitUpdateInterval;
	use TraitDetectNextAutomaticBadge;

	private static ?array $aSettings
		= [
			'setting' => 'new_arrival'
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

	/**
	 * @throws Exception
	 */
	public function isAppliedBadge(): bool
	{
		$aResponse = $this->response();

		return $aResponse['status'] == 'success';
	}

	/**
	 * @throws Exception
	 */
	public function response(): array
	{
		if (!$this->hasSetting()) {
			return MessageFactory::factory()->error(esc_html__('This product has not added to the On Sale Badge',
				MYSHOPKIT_MB_WP_REST_NAMESPACE),
				400);
		}

		if (!$this->isNewArrival()) {
			return MessageFactory::factory()->error(esc_html__('This product is not on sale',
				MYSHOPKIT_MB_WP_REST_NAMESPACE),
				400);
		}

		$this->oAutomaticContext->applyProductBadge(self::$aSettings);

		return MessageFactory::factory()->success(esc_html__('Applied Badge', MYSHOPKIT_MB_WP_REST_NAMESPACE));
	}

	private function hasSetting(): bool
	{
		if (self::$aSettings !== null) {
			$aPosts = get_posts([
				'post_type'      => AutoPrefix::namePrefix('new_arrival'),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'author'         => $this->oAutomaticContext->getUserID()
			]);
		}
		if (empty($aPosts)) {
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
	 * @throws Exception
	 */
	private function isNewArrival(): bool
	{
		$createdAt = $this->oAutomaticContext->getProductInfo()['createDate'];
		$postID = $this->oAutomaticContext->getProductInfo()['id'];

		$oDate1 = new DateTime($createdAt);
		$oDate2 = new DateTime();
		$oDate2->setTimezone(new DateTimeZone("UTC")); //GMT
		$interval = $oDate2->diff($oDate1)->format('%a');
		return $interval <= $this->getInterval($postID);
	}
}
