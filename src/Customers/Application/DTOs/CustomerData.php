<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Customers\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Customers\Application\DTOs;

use Spatie\LaravelData\Data;

final class CustomerData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly int     $user_id,
        public readonly ?string $customer_group_id,
        public readonly ?string $customer_group_name,
        public readonly string  $first_name,
        public readonly string  $last_name,
        public readonly string  $email,
        public readonly ?string $phone,
        public readonly ?string $company,
        public readonly ?string $gender,
        public readonly bool    $is_active,
        public readonly bool    $is_subscribed_to_newsletter,
        public readonly ?string $last_login_at,
        public readonly string  $created_at,
    ) {}

    public static function fromModel(object $customer): self
    {
        return new self(
            id:                          $customer->id,
            user_id:                     $customer->user_id,
            customer_group_id:           $customer->customer_group_id,
            customer_group_name:         $customer->customerGroup?->name,
            first_name:                  $customer->first_name,
            last_name:                   $customer->last_name,
            email:                       $customer->user->email,
            phone:                       $customer->phone,
            company:                     $customer->company,
            gender:                      $customer->gender,
            is_active:                   (bool) $customer->is_active,
            is_subscribed_to_newsletter: (bool) $customer->is_subscribed_to_newsletter,
            last_login_at:               $customer->last_login_at?->toIso8601String(),
            created_at:                  $customer->created_at->toIso8601String(),
        );
    }
}
