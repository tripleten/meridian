<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Application\DTOs;

use Spatie\LaravelData\Data;

final class PriceListData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $code,
        public readonly ?string $channel_id,
        public readonly ?string $customer_group_id,
        public readonly ?string $customer_group_name,
        public readonly string  $currency_code,
        public readonly bool    $is_default,
        public readonly bool    $is_active,
        public readonly int     $item_count,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $priceList): self
    {
        return new self(
            id:                  (string) $priceList->id,
            name:                $priceList->name,
            code:                $priceList->code,
            channel_id:          $priceList->channel_id ? (string) $priceList->channel_id : null,
            customer_group_id:   $priceList->customer_group_id ? (string) $priceList->customer_group_id : null,
            customer_group_name: $priceList->customerGroup?->name,
            currency_code:       $priceList->currency_code,
            is_default:          (bool) $priceList->is_default,
            is_active:           (bool) $priceList->is_active,
            item_count:          (int) ($priceList->price_list_items_count ?? 0),
            created_at:          $priceList->created_at->toDateTimeString(),
        );
    }
}
