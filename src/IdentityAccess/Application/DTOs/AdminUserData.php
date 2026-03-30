<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Application\DTOs;

use App\Models\User;
use Spatie\LaravelData\Data;

final class AdminUserData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $email,
        public readonly array   $roles,
        public readonly bool    $has_two_factor,
        public readonly ?string $email_verified_at,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id:                 $user->id,
            name:               $user->name,
            email:              $user->email,
            roles:              $user->getRoleNames()->values()->toArray(),
            has_two_factor:     ! is_null($user->two_factor_confirmed_at),
            email_verified_at:  $user->email_verified_at?->toIso8601String(),
            created_at:         $user->created_at->toIso8601String(),
        );
    }
}
