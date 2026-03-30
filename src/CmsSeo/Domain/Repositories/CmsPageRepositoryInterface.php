<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Domain\Repositories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Domain\Repositories;

interface CmsPageRepositoryInterface
{
    public function findById(string $id): ?object;

    public function findByUrlKey(string $urlKey, ?string $channelId): ?object;

    public function paginate(array $filters, int $perPage): mixed;

    public function save(object $page): void;

    public function delete(string $id): void;
}
