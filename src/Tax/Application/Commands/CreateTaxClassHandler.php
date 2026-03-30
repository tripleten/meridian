<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Application\Commands;

use Illuminate\Support\Str;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxClass;

final readonly class CreateTaxClassHandler
{
    public function handle(CreateTaxClassCommand $command): void
    {
        $exists = EloquentTaxClass::where('code', $command->code)->exists();

        if ($exists) {
            throw new DomainException("A tax class with code '{$command->code}' already exists.");
        }

        EloquentTaxClass::create([
            'id'   => (string) Str::ulid(),
            'name' => $command->name,
            'code' => $command->code,
        ]);
    }
}
