<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Application\Commands;

use Illuminate\Support\Str;
use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsPage;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class CreateCmsPageHandler
{
    public function handle(CreateCmsPageCommand $command): EloquentCmsPage
    {
        $exists = EloquentCmsPage::where('url_key', $command->url_key)
            ->where('channel_id', $command->channel_id)
            ->exists();

        if ($exists) {
            throw new DomainException("A page with url_key '{$command->url_key}' already exists for this channel.");
        }

        $publishedAt = null;
        if ($command->state === 'published') {
            $publishedAt = now();
        }

        return EloquentCmsPage::create([
            'id'                  => (string) Str::ulid(),
            'channel_id'          => $command->channel_id,
            'title'               => $command->title,
            'url_key'             => $command->url_key,
            'content'             => $command->content,
            'state'               => $command->state,
            'meta_title'          => $command->meta_title,
            'meta_description'    => $command->meta_description,
            'meta_keywords'       => $command->meta_keywords,
            'meta_robots_noindex' => $command->meta_robots_noindex,
            'published_at'        => $publishedAt,
        ]);
    }
}
