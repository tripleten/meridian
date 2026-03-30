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

use Meridian\Pricing\Domain\Repositories\PriceListRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final readonly class UpdatePriceListHandler
{
    public function __construct(private PriceListRepositoryInterface $priceLists) {}

    public function handle(UpdatePriceListCommand $command): void
    {
        $priceList = $this->priceLists->findById($command->priceListId);

        if ($priceList === null) {
            throw new DomainException("Price list '{$command->priceListId}' not found.");
        }

        if ($command->is_default && ! $priceList->is_default) {
            \Meridian\Pricing\Infrastructure\Persistence\EloquentPriceList::where('is_default', true)
                ->where('id', '!=', $command->priceListId)
                ->update(['is_default' => false]);
        }

        $priceList->name              = $command->name;
        $priceList->code              = $command->code;
        $priceList->currency_code     = $command->currency_code;
        $priceList->channel_id        = $command->channel_id;
        $priceList->customer_group_id = $command->customer_group_id;
        $priceList->is_default        = $command->is_default;
        $priceList->is_active         = $command->is_active;

        $this->priceLists->save($priceList);
    }
}
