<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Domain\Repositories;

interface PriceListRepositoryInterface
{
    public function findById(string $id): ?object;
    public function findByCode(string $code): ?object;
    public function all(bool $activeOnly = false): array;
    public function save(object $priceList): void;
    public function delete(string $id): void;
}
