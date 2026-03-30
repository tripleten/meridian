<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Domain\Repositories;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?object;

    public function findBySlug(string $slug): ?object;

    public function findBySku(string $sku): ?object;

    public function paginate(array $filters = [], int $perPage = 20): mixed;

    public function save(object $product): void;

    public function delete(string $id): void;
}
