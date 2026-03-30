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
use Meridian\Promotions\Domain\CouponType;

class EloquentCoupon extends Model
{
    protected $table = 'coupons';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'code',
        'description',
        'type',
        'usage_limit',
        'usage_limit_per_customer',
        'times_used',
        'cart_rule_id',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'type'                     => CouponType::class,
        'is_active'                => 'boolean',
        'usage_limit'              => 'integer',
        'usage_limit_per_customer' => 'integer',
        'times_used'               => 'integer',
        'valid_from'               => 'datetime',
        'valid_until'              => 'datetime',
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
