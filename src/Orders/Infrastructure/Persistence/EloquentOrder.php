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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Meridian\Customers\Infrastructure\Persistence\EloquentCustomer;
use Meridian\Orders\Domain\Order\OrderStatus;
use Meridian\Orders\Domain\Order\PaymentStatus;

class EloquentOrder extends Model
{
    protected $table = 'orders';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'number', 'channel_id', 'user_id', 'customer_id', 'customer_email',
        'status', 'payment_status', 'base_currency', 'order_currency', 'exchange_rate_snapshot',
        'subtotal', 'discount_amount', 'shipping_amount', 'tax_amount', 'grand_total',
        'base_subtotal', 'base_grand_total', 'base_tax_amount', 'total_refunded',
        'coupon_code', 'applied_rule_ids', 'payment_method', 'payment_method_id',
        'shipping_method', 'shipping_carrier',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'shipping_address_snapshot', 'billing_address_snapshot',
        'pricing_snapshot', 'tax_snapshot', 'customer_snapshot',
        'invoice_number', 'invoiced_at', 'customer_vat_number', 'vat_number_valid',
        'customer_note', 'placed_at',
    ];

    protected $casts = [
        'subtotal'                  => 'integer',
        'discount_amount'           => 'integer',
        'shipping_amount'           => 'integer',
        'tax_amount'                => 'integer',
        'grand_total'               => 'integer',
        'base_subtotal'             => 'integer',
        'base_grand_total'          => 'integer',
        'base_tax_amount'           => 'integer',
        'total_refunded'            => 'integer',
        'shipping_address_snapshot' => 'array',
        'billing_address_snapshot'  => 'array',
        'pricing_snapshot'          => 'array',
        'tax_snapshot'              => 'array',
        'customer_snapshot'         => 'array',
        'applied_rule_ids'          => 'array',
        'placed_at'                 => 'datetime',
        'invoiced_at'               => 'datetime',
        'vat_number_valid'          => 'boolean',
        'status'                    => OrderStatus::class,
        'payment_status'            => PaymentStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(EloquentOrderItem::class, 'order_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(EloquentOrderComment::class, 'order_id')->orderByDesc('created_at');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(EloquentOrderRefund::class, 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(EloquentCustomer::class, 'customer_id');
    }
}
