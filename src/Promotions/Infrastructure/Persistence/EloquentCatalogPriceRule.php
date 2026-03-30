<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Meridian\Promotions\Domain\CatalogDiscountType;

class EloquentCatalogPriceRule extends Model
{
    protected $table = 'catalog_price_rules';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'description', 'channel_id',
        'customer_group_ids', 'category_ids',
        'discount_type', 'discount_amount',
        'is_active', 'priority', 'stop_further_rules',
        'valid_from', 'valid_until',
    ];

    protected $casts = [
        'discount_type'       => CatalogDiscountType::class,
        'discount_amount'     => 'float',
        'category_ids'        => 'array',
        'customer_group_ids'  => 'array',
        'is_active'           => 'boolean',
        'stop_further_rules'  => 'boolean',
        'valid_from'          => 'datetime',
        'valid_until'         => 'datetime',
        'priority'            => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }
}
