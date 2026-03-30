<?php

declare(strict_types=1);

namespace Meridian\Payments\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Meridian\Payments\Domain\TransactionState;
use Meridian\Payments\Domain\TransactionType;

class EloquentTransaction extends Model
{
    protected $table = 'transactions';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_id', 'payment_method_id',
        'type', 'state', 'amount', 'currency_code',
        'gateway_transaction_id', 'gateway_response', 'notes',
    ];

    protected $casts = [
        'type'             => TransactionType::class,
        'state'            => TransactionState::class,
        'amount'           => 'integer',
        'gateway_response' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(EloquentPaymentMethod::class, 'payment_method_id');
    }
}
