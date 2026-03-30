<?php

declare(strict_types=1);

namespace Meridian\Payments\Domain;

enum TransactionState: string
{
    case Pending   = 'pending';
    case Success   = 'success';
    case Failed    = 'failed';
    case Voided    = 'voided';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::Success => 'Success',
            self::Failed  => 'Failed',
            self::Voided  => 'Voided',
        };
    }

    public function badgeVariant(): string
    {
        return match($this) {
            self::Pending => 'secondary',
            self::Success => 'default',
            self::Failed  => 'destructive',
            self::Voided  => 'outline',
        };
    }
}
