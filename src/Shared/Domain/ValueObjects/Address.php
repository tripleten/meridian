<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Domain\ValueObjects
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Domain\ValueObjects;

/**
 * Immutable postal address value object.
 *
 * Used for shipping addresses, billing addresses, and customer address book entries.
 * Stored as a JSON snapshot on orders to preserve the address at placement time.
 */
final readonly class Address
{
    public function __construct(
        public readonly string  $firstName,
        public readonly string  $lastName,
        public readonly string  $line1,
        public readonly ?string $line2,
        public readonly string  $city,
        public readonly ?string $county,
        public readonly string  $postcode,
        public readonly string  $countryCode,  // ISO 3166-1 alpha-2 (e.g. 'GB', 'US')
        public readonly ?string $phone,
        public readonly ?string $company,
        public readonly ?string $vatNumber,
    ) {}

    public function fullName(): string
    {
        return trim("{$this->firstName} {$this->lastName}");
    }

    public function isUk(): bool
    {
        return $this->countryCode === 'GB';
    }

    public function isEu(): bool
    {
        return in_array($this->countryCode, [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
            'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT',
            'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
        ], true);
    }

    public function equals(self $other): bool
    {
        return $this->line1 === $other->line1
            && $this->postcode === $other->postcode
            && $this->countryCode === $other->countryCode;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'first_name'   => $this->firstName,
            'last_name'    => $this->lastName,
            'line1'        => $this->line1,
            'line2'        => $this->line2,
            'city'         => $this->city,
            'county'       => $this->county,
            'postcode'     => $this->postcode,
            'country_code' => $this->countryCode,
            'phone'        => $this->phone,
            'company'      => $this->company,
            'vat_number'   => $this->vatNumber,
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName:   $data['first_name'],
            lastName:    $data['last_name'],
            line1:       $data['line1'],
            line2:       $data['line2'] ?? null,
            city:        $data['city'],
            county:      $data['county'] ?? null,
            postcode:    $data['postcode'],
            countryCode: $data['country_code'],
            phone:       $data['phone'] ?? null,
            company:     $data['company'] ?? null,
            vatNumber:   $data['vat_number'] ?? null,
        );
    }
}
