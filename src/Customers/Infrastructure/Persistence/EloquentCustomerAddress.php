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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class EloquentCustomerAddress extends Model
{
    protected $table      = 'customer_addresses';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'label',
        'first_name',
        'last_name',
        'company',
        'line1',
        'line2',
        'city',
        'county',
        'postcode',
        'country_code',
        'phone',
        'vat_number',
        'is_default_billing',
        'is_default_shipping',
    ];

    protected $casts = [
        'is_default_billing'  => 'boolean',
        'is_default_shipping' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $address): void {
            if (empty($address->id)) {
                $address->id = (string) Str::ulid();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(EloquentCustomer::class, 'customer_id');
    }
}
