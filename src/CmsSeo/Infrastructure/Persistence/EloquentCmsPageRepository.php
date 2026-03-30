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

use Meridian\CmsSeo\Domain\Repositories\CmsPageRepositoryInterface;

final class EloquentCmsPageRepository implements CmsPageRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return EloquentCmsPage::find($id);
    }

    public function findByUrlKey(string $urlKey, ?string $channelId): ?object
    {
        return EloquentCmsPage::where('url_key', $urlKey)
            ->where('channel_id', $channelId)
            ->first();
    }

    public function paginate(array $filters, int $perPage): mixed
    {
        $builder = EloquentCmsPage::query();

        if (! empty($filters['search'])) {
            $builder->where('title', 'like', "%{$filters['search']}%");
        }

        if (! empty($filters['state'])) {
            $builder->where('state', $filters['state']);
        }

        return $builder->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function save(object $page): void
    {
        /** @var EloquentCmsPage $page */
        $page->save();
    }

    public function delete(string $id): void
    {
        EloquentCmsPage::destroy($id);
    }
}
