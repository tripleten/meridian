<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Application\Commands;

use Illuminate\Support\Str;
use Meridian\Pricing\Infrastructure\Persistence\EloquentPriceList;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class CreatePriceListHandler
{
    public function handle(CreatePriceListCommand $command): void
    {
        if (EloquentPriceList::where('code', $command->code)->exists()) {
            throw new DomainException("A price list with code '{$command->code}' already exists.");
        }

        if ($command->is_default) {
            EloquentPriceList::where('is_default', true)->update(['is_default' => false]);
        }

        EloquentPriceList::create([
            'id'                => (string) Str::ulid(),
            'name'              => $command->name,
            'code'              => $command->code,
            'currency_code'     => $command->currency_code,
            'channel_id'        => $command->channel_id,
            'customer_group_id' => $command->customer_group_id,
            'is_default'        => $command->is_default,
            'is_active'         => $command->is_active,
        ]);
    }
}
