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

use Illuminate\Support\Str;
use Meridian\Catalog\Infrastructure\Persistence\EloquentBrand;

final readonly class CreateBrandHandler
{
    public function handle(CreateBrandCommand $command): void
    {
        EloquentBrand::create([
            'id'          => (string) Str::ulid(),
            'name'        => $command->name,
            'slug'        => $command->slug,
            'description' => $command->description,
            'is_active'   => $command->is_active,
        ]);
    }
}
