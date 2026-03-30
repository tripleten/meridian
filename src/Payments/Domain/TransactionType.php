<?php

declare(strict_types=1);

namespace Meridian\Payments\Domain;

enum TransactionType: string
{
    case Capture   = 'capture';
    case Authorize = 'authorize';
    case Refund    = 'refund';
    case Void      = 'void';

    public function label(): string
    {
        return match($this) {
            self::Capture   => 'Capture',
            self::Authorize => 'Authorize',
            self::Refund    => 'Refund',
            self::Void      => 'Void',
        };
    }
}
