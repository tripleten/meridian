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

use InvalidArgumentException;

/**
 * Immutable URL key (slug) value object.
 *
 * Enforces lowercase, hyphen-separated format suitable for SEO-friendly URLs.
 * e.g. 'mens-running-shoes', 'vitamin-c-1000mg'
 */
final readonly class UrlKey
{
    private function __construct(public readonly string $value) {}

    /**
     * Create a UrlKey from an already-valid slug string.
     *
     * @throws InvalidArgumentException if the value contains invalid characters
     */
    public static function fromString(string $value): self
    {
        $normalised = strtolower(trim($value));

        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $normalised)) {
            throw new InvalidArgumentException(
                "Invalid URL key '{$value}'. Must be lowercase alphanumeric with hyphens only."
            );
        }

        return new self($normalised);
    }

    /**
     * Generate a UrlKey by slugifying a human-readable string.
     * e.g. "Men's Running Shoes" → "mens-running-shoes"
     */
    public static function fromLabel(string $label): self
    {
        $slug = strtolower($label);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return new self($slug);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
