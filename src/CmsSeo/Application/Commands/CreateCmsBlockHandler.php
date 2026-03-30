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
use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsBlock;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class CreateCmsBlockHandler
{
    public function handle(CreateCmsBlockCommand $command): EloquentCmsBlock
    {
        $exists = EloquentCmsBlock::where('identifier', $command->identifier)
            ->where('channel_id', $command->channel_id)
            ->exists();

        if ($exists) {
            throw new DomainException("A block with identifier '{$command->identifier}' already exists for this channel.");
        }

        return EloquentCmsBlock::create([
            'id'         => (string) Str::ulid(),
            'channel_id' => $command->channel_id,
            'identifier' => $command->identifier,
            'title'      => $command->title,
            'content'    => $command->content,
            'is_active'  => $command->is_active,
        ]);
    }
}
