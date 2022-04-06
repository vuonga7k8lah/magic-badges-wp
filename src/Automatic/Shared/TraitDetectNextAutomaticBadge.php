<?php

namespace MyShopKitMBWP\Automatic\Shared;

use Exception;

trait TraitDetectNextAutomaticBadge
{
	private ?AutomaticContext $oAutomaticContext;

	/**
	 * @throws Exception
	 */
	private function getNextAutomaticBadge(IAutomaticState $oCurrentAutomaticBadge): IAutomaticState
	{
		$order = $this->detectCurrentAutomaticBadgeOrder($oCurrentAutomaticBadge);
		if ($this->isTheEnd($order)) {
			throw new \Exception(esc_html__('The product has not added to any badge', MYSHOPKIT_MB_WP_REST_NAMESPACE));
		}

		return new $this->aAutomaticBadgesOrder[$order + 1]($this->oAutomaticContext);
	}

	/**
	 * @throws Exception
	 */
	private function detectCurrentAutomaticBadgeOrder(IAutomaticState $oCurrentAutomaticBadge)
	{
		$order = null;
		foreach ($this->aAutomaticBadgesOrder as $order => $className) {
			if ($oCurrentAutomaticBadge instanceof $className) {
				return $order;
			}
		}

		if ($order === null) {
			throw new Exception(esc_html__('The Object does not belong to any Classes', MYSHOPKIT_MB_WP_REST_NAMESPACE));
		}
	}

	private function isTheEnd($order): bool
	{
		if (count($this->aAutomaticBadgesOrder) == $order + 1) {
			return true;
		}

		return false;
	}
}
