<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class EloquentCustomerGroup extends Model
{
    protected $table      = 'customer_groups';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $group): void {
            if (empty($group->id)) {
                $group->id = (string) Str::ulid();
            }
        });
    }

    public function customers(): HasMany
    {
        return $this->hasMany(EloquentCustomer::class, 'customer_group_id');
    }
}
