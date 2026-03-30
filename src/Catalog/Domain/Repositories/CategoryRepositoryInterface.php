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

interface CategoryRepositoryInterface
{
    public function findById(string $id): ?object;

    public function all(): mixed; // flat list for admin tree

    public function save(object $category): void;

    public function delete(string $id): void;
}
