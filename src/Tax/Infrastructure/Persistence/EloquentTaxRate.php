<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EloquentTaxRate extends Model
{
    protected $table = 'tax_rates';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'tax_zone_id',
        'name',
        'code',
        'rate',
        'type',
        'is_compound',
        'is_shipping_taxable',
    ];

    protected $casts = [
        'rate'                => 'float',
        'is_compound'         => 'boolean',
        'is_shipping_taxable' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function taxZone(): BelongsTo
    {
        return $this->belongsTo(EloquentTaxZone::class, 'tax_zone_id');
    }
}
