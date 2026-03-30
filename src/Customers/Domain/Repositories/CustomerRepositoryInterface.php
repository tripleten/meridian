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

interface CustomerRepositoryInterface
{
    public function findById(string $id): ?object;

    public function findByUserId(int $userId): ?object;

    public function paginate(array $filters, int $perPage): mixed;

    public function save(object $customer): void;

    public function delete(string $id): void;
}
