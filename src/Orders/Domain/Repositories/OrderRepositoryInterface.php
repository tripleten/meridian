<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Domain\Repositories;

interface OrderRepositoryInterface
{
    public function findById(string $id): ?object;

    public function findByNumber(string $number): ?object;

    public function paginate(array $filters, int $perPage): mixed;

    public function save(object $order): void;
}
