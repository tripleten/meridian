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

final class CustomerAddressData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $customer_id,
        public readonly ?string $label,
        public readonly string  $first_name,
        public readonly string  $last_name,
        public readonly ?string $company,
        public readonly string  $line1,
        public readonly ?string $line2,
        public readonly string  $city,
        public readonly ?string $county,
        public readonly string  $postcode,
        public readonly string  $country_code,
        public readonly ?string $phone,
        public readonly bool    $is_default_billing,
        public readonly bool    $is_default_shipping,
    ) {}

    public static function fromModel(object $address): self
    {
        return new self(
            id:                  $address->id,
            customer_id:         $address->customer_id,
            label:               $address->label,
            first_name:          $address->first_name,
            last_name:           $address->last_name,
            company:             $address->company,
            line1:               $address->line1,
            line2:               $address->line2,
            city:                $address->city,
            county:              $address->county,
            postcode:            $address->postcode,
            country_code:        $address->country_code,
            phone:               $address->phone,
            is_default_billing:  (bool) $address->is_default_billing,
            is_default_shipping: (bool) $address->is_default_shipping,
        );
    }
}
