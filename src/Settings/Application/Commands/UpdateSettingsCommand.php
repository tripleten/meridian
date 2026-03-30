<?php

declare(strict_types=1);

namespace Meridian\Settings\Application\Commands;

final class UpdateSettingsCommand
{
    public function __construct(
        public readonly string $group,
        public readonly array  $values,  // key => value
        public readonly int    $updatedBy,
    ) {}
}
