<?php

namespace MyShopKitMBWP\Shared\Product\Interfaces;

interface IPlatform
{
    public function getProductsBySlug(array $aSlug, $customerID);

    public function parseProducts(array $aProducts): array;

    public function search($titleKeyword, $customerID, array $aArgs = [], $isExtract = false): array;

    public function getProducts($customerID, array $aArgs = []): array;

    public function getCost(): array;

    public function getLastCursor(): string;

    public function hasNextPage(): bool;
}
