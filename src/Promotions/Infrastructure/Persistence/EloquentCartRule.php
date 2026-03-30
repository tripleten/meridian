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
use Meridian\Promotions\Domain\CartDiscountType;

class EloquentCartRule extends Model
{
    protected $table = 'cart_rules';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'description', 'channel_id', 'customer_group_ids',
        'discount_type', 'discount_amount', 'discount_qty',
        'apply_to_shipping', 'stop_rules_processing', 'conditions',
        'is_active', 'valid_from', 'valid_until',
        'uses_per_coupon', 'uses_per_customer', 'sort_order',
    ];

    protected $casts = [
        'discount_type'          => CartDiscountType::class,
        'discount_amount'        => 'float',
        'discount_qty'           => 'integer',
        'apply_to_shipping'      => 'boolean',
        'stop_rules_processing'  => 'boolean',
        'conditions'             => 'array',
        'customer_group_ids'     => 'array',
        'is_active'              => 'boolean',
        'valid_from'             => 'datetime',
        'valid_until'            => 'datetime',
        'sort_order'             => 'integer',
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
