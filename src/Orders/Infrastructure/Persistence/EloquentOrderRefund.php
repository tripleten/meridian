<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EloquentOrderRefund extends Model
{
    protected $table = 'order_refunds';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_id', 'credit_memo_number', 'state',
        'subtotal', 'tax_amount', 'shipping_amount', 'total',
        'items_snapshot', 'reason', 'gateway_refund_id',
        'processed_by', 'processed_at',
    ];

    protected $casts = [
        'subtotal'        => 'integer',
        'tax_amount'      => 'integer',
        'shipping_amount' => 'integer',
        'total'           => 'integer',
        'items_snapshot'  => 'array',
        'processed_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(EloquentOrder::class, 'order_id');
    }
}
