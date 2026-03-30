<?php

declare(strict_types=1);

namespace Meridian\Settings\Application\Commands;

use Meridian\Settings\Infrastructure\Persistence\EloquentSetting;

final class UpdateSettingsHandler
{
    public function handle(UpdateSettingsCommand $command): void
    {
        foreach ($command->values as $key => $value) {
            EloquentSetting::updateOrCreate(
                ['group' => $command->group, 'key' => $key],
                ['value' => $value, 'updated_by' => $command->updatedBy],
            );
        }
    }
}
