<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Domain\Events
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Domain\Events;

use DateTimeImmutable;
use Meridian\Shared\Domain\Events\DomainEvent;

final class AdminUserInvited extends DomainEvent
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
        public readonly string $role,
        DateTimeImmutable      $occurredAt = new DateTimeImmutable(),
    ) {
        parent::__construct($occurredAt);
    }

    public function aggregateType(): string
    {
        return 'AdminUser';
    }

    public function aggregateId(): string
    {
        return (string) $this->userId;
    }

    public function toPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'email'   => $this->email,
            'role'    => $this->role,
        ];
    }
}
