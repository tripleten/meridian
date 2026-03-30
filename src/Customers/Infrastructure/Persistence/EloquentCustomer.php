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

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class EloquentCustomer extends Model
{
    protected $table      = 'customers';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'customer_group_id',
        'first_name',
        'last_name',
        'phone',
        'company',
        'vat_number',
        'gender',
        'is_active',
        'is_subscribed_to_newsletter',
        'last_login_at',
    ];

    protected $casts = [
        'is_active'                   => 'boolean',
        'is_subscribed_to_newsletter' => 'boolean',
        'vat_validated'               => 'boolean',
        'last_login_at'               => 'datetime',
        'date_of_birth'               => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $customer): void {
            if (empty($customer->id)) {
                $customer->id = (string) Str::ulid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(EloquentCustomerGroup::class, 'customer_group_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(EloquentCustomerAddress::class, 'customer_id');
    }
}
