<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Infrastructure\Persistence;

use Meridian\CmsSeo\Domain\Repositories\CmsBlockRepositoryInterface;

final class EloquentCmsBlockRepository implements CmsBlockRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return EloquentCmsBlock::find($id);
    }

    public function findByIdentifier(string $identifier, ?string $channelId): ?object
    {
        return EloquentCmsBlock::where('identifier', $identifier)
            ->where('channel_id', $channelId)
            ->first();
    }

    public function all(array $filters = []): array
    {
        $builder = EloquentCmsBlock::query();

        if (isset($filters['is_active'])) {
            $builder->where('is_active', $filters['is_active']);
        }

        return $builder->orderBy('title')->get()->all();
    }

    public function save(object $block): void
    {
        /** @var EloquentCmsBlock $block */
        $block->save();
    }

    public function delete(string $id): void
    {
        EloquentCmsBlock::destroy($id);
    }
}
