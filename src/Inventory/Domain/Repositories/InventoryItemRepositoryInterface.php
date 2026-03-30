<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Domain\Repositories;

interface InventoryItemRepositoryInterface
{
    public function findByVariantAndSource(string $variantId, string $sourceId): ?object;

    public function findByVariant(string $variantId): array;

    public function save(object $item): void;
}
