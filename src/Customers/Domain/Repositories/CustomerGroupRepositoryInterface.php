<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Domain\Repositories;

interface CustomerGroupRepositoryInterface
{
    public function findById(string $id): ?object;

    public function findByCode(string $code): ?object;

    public function all(): array;

    public function save(object $group): void;
}
