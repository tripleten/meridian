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

/**
 * Request-scoped channel context.
 *
 * Set once per request by ChannelContextMiddleware and injected into
 * repositories and services that need channel-scoped queries.
 * Never stored as a static global — injectable only.
 */
final readonly class ChannelContext
{
    public function __construct(
        public readonly string $channelId,
        public readonly string $channelCode,
        public readonly string $locale,
    ) {}

    public function isDefault(): bool
    {
        return $this->channelCode === 'default';
    }
}
