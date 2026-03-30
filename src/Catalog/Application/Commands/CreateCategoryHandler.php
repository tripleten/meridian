<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Meridian\Catalog\Infrastructure\Persistence\EloquentCategory;

final readonly class CreateCategoryHandler
{
    public function handle(CreateCategoryCommand $command): void
    {
        DB::transaction(function () use ($command): void {
            EloquentCategory::create([
                'id'               => (string) Str::ulid(),
                'name'             => $command->name,
                'url_key'          => $command->url_key,
                'parent_id'        => $command->parent_id,
                'description'      => $command->description,
                'is_active'       => $command->is_active,
                'seo_title'       => $command->meta_title,
                'seo_description' => $command->meta_description,
            ]);
        });
    }
}
