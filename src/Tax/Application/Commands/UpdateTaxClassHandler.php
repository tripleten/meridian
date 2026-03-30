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

use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxClass;

final readonly class UpdateTaxClassHandler
{
    public function handle(UpdateTaxClassCommand $command): void
    {
        /** @var EloquentTaxClass $taxClass */
        $taxClass = EloquentTaxClass::findOrFail($command->id);

        $duplicate = EloquentTaxClass::where('code', $command->code)
            ->where('id', '!=', $command->id)
            ->exists();

        if ($duplicate) {
            throw new DomainException("A tax class with code '{$command->code}' already exists.");
        }

        $taxClass->name = $command->name;
        $taxClass->code = $command->code;
        $taxClass->save();
    }
}
