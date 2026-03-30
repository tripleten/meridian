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
 * Immutable SEO metadata value object.
 *
 * Embedded directly on Category, Product, and CMS Page models — not a
 * separate polymorphic table. Avoids N+1 joins and simplifies reads.
 *
 * When a field is null, the consuming code is expected to generate a
 * sensible fallback (e.g. product name as meta title).
 */
final readonly class SeoMeta
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?string $keywords,
        public readonly ?string $canonicalUrl,
        public readonly string  $robots,       // 'index,follow' | 'noindex,nofollow' etc.
        public readonly ?string $ogTitle,
        public readonly ?string $ogDescription,
        public readonly ?string $ogImageUrl,
    ) {}

    public static function empty(): self
    {
        return new self(
            title:          null,
            description:    null,
            keywords:       null,
            canonicalUrl:   null,
            robots:         'index,follow',
            ogTitle:        null,
            ogDescription:  null,
            ogImageUrl:     null,
        );
    }

    public function withTitle(string $title): self
    {
        return new self(
            title:          $title,
            description:    $this->description,
            keywords:       $this->keywords,
            canonicalUrl:   $this->canonicalUrl,
            robots:         $this->robots,
            ogTitle:        $this->ogTitle,
            ogDescription:  $this->ogDescription,
            ogImageUrl:     $this->ogImageUrl,
        );
    }

    public function isIndexable(): bool
    {
        return str_contains($this->robots, 'index') && ! str_contains($this->robots, 'noindex');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'seo_title'       => $this->title,
            'seo_description' => $this->description,
            'seo_keywords'    => $this->keywords,
            'canonical_url'   => $this->canonicalUrl,
            'seo_robots'      => $this->robots,
            'og_title'        => $this->ogTitle,
            'og_description'  => $this->ogDescription,
            'og_image_url'    => $this->ogImageUrl,
        ];
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            title:          $data['seo_title'] ?? null,
            description:    $data['seo_description'] ?? null,
            keywords:       $data['seo_keywords'] ?? null,
            canonicalUrl:   $data['canonical_url'] ?? null,
            robots:         $data['seo_robots'] ?? 'index,follow',
            ogTitle:        $data['og_title'] ?? null,
            ogDescription:  $data['og_description'] ?? null,
            ogImageUrl:     $data['og_image_url'] ?? null,
        );
    }
}
