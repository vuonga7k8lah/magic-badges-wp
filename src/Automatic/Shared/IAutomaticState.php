<?php

namespace MyShopKitMBWP\Automatic\Shared;

interface IAutomaticState
{
	public function proceedToNextBadge(): IAutomaticState;

	public function response(): array;

	public function isAppliedBadge(): bool;

	public function __construct(AutomaticContext $oAutomaticContext);
}
