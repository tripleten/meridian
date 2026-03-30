<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Infrastructure\Outbox
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Infrastructure\Outbox;

use Illuminate\Support\Facades\DB;

/**
 * Prevents queue jobs from producing duplicate side effects on retry.
 *
 * Usage in a job:
 *   if (IdempotencyLog::alreadyProcessed("order_confirmation:{$this->orderId}")) {
 *       return;
 *   }
 *   // ... do the work ...
 *   IdempotencyLog::markProcessed("order_confirmation:{$this->orderId}", ttl: 604800);
 */
final class IdempotencyLog
{
    /**
     * Check if a key has already been processed.
     */
    public static function alreadyProcessed(string $key): bool
    {
        return DB::table('idempotency_log')
            ->where('idempotency_key', $key)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Record a key as processed.
     *
     * @param  int $ttl  Seconds to retain the record (default 7 days)
     */
    public static function markProcessed(string $key, int $ttl = 604800): void
    {
        DB::table('idempotency_log')->upsert(
            [
                'idempotency_key' => $key,
                'processed_at'    => now(),
                'expires_at'      => now()->addSeconds($ttl),
            ],
            uniqueBy: ['idempotency_key'],
            update:   ['processed_at', 'expires_at'],
        );
    }
}
