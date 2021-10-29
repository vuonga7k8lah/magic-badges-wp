<?php

namespace MyShopKitMB\Automatic\Shared;

class AutomaticContext
{
	use TraitSetAutomaticBadges;

	private array           $aProduct = [];
	public ?IAutomaticState $oState   = null;
	private ?string         $userID;

	public function __construct()
	{
		$oTopAutomaticBadge = new $this->aAutomaticBadgesOrder[0]($this);
		$this->setState($oTopAutomaticBadge);
	}

	/**
	 * Set product information
	 * @param array $aProduct
	 * @return AutomaticContext
	 */
	public function setProductInfo(array $aProduct): AutomaticContext
	{
		$this->aProduct = $aProduct;
		return $this;
	}

	public function getProductInfo(): array
	{
		return $this->aProduct;
	}

	public function applyProductBadge(array $aBadge): array
	{
		$this->aProduct['badge'] = $aBadge;
		return $this->aProduct;
	}

	public function setUserID($userID): AutomaticContext
	{
		$this->userID = (string)$userID;
		return $this;
	}

	public function getUserID(): string
	{
		return $this->userID;
	}


	public function setState(IAutomaticState $oState): AutomaticContext
	{
		$this->oState = $oState;

		return $this;
	}
}
