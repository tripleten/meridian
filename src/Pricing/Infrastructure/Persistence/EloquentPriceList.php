<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomerGroup;

class EloquentPriceList extends Model
{
    protected $table = 'price_lists';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'code', 'channel_id', 'customer_group_id',
        'currency_code', 'is_default', 'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(EloquentCustomerGroup::class, 'customer_group_id');
    }

    public function priceListItems(): HasMany
    {
        return $this->hasMany(EloquentPriceListItem::class, 'price_list_id');
    }
}
