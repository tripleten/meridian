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

interface InventorySourceRepositoryInterface
{
    public function findById(string $id): ?object;

    public function findByCode(string $code): ?object;

    public function all(): array;

    public function save(object $source): void;
}
