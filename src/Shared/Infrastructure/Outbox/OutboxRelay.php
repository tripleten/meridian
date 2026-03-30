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
use Illuminate\Support\Facades\Log;

/**
 * Reads committed outbox_messages rows and dispatches them to the queue.
 *
 * Run via: php artisan outbox:relay
 * Scheduled every 5 seconds via Horizon or a daemon process.
 *
 * Each row is locked with SELECT FOR UPDATE to prevent concurrent relay
 * workers from processing the same event twice.
 */
final class OutboxRelay
{
    private const BATCH_SIZE = 50;

    /** @param array<string, string> $eventJobMap  Maps event FQCN → job FQCN */
    public function __construct(
        private readonly array $eventJobMap,
    ) {}

    /**
     * Process a single batch of unrelayed outbox messages.
     * Returns the number of messages dispatched.
     */
    public function relay(): int
    {
        $dispatched = 0;

        $rows = DB::table('outbox_messages')
            ->whereNull('dispatched_at')
            ->where('attempts', '<', 5)
            ->orderBy('occurred_at')
            ->limit(self::BATCH_SIZE)
            ->get();

        foreach ($rows as $row) {
            DB::transaction(function () use ($row, &$dispatched) {
                // Lock the row to prevent concurrent relay workers
                $locked = DB::table('outbox_messages')
                    ->where('id', $row->id)
                    ->whereNull('dispatched_at')
                    ->lockForUpdate()
                    ->first();

                if ($locked === null) {
                    // Another worker already processed this row
                    return;
                }

                $this->dispatchEvent($row);

                DB::table('outbox_messages')
                    ->where('id', $row->id)
                    ->update(['dispatched_at' => now()]);

                $dispatched++;
            });
        }

        return $dispatched;
    }

    private function dispatchEvent(object $row): void
    {
        $jobClass = $this->eventJobMap[$row->event_type] ?? null;

        if ($jobClass === null) {
            Log::warning("OutboxRelay: no job mapped for event type '{$row->event_type}'", [
                'outbox_id' => $row->id,
            ]);

            DB::table('outbox_messages')
                ->where('id', $row->id)
                ->increment('attempts');

            return;
        }

        $payload = json_decode($row->payload, true, flags: JSON_THROW_ON_ERROR);
        dispatch(new $jobClass($payload, $row->id));
    }
}
