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
use Meridian\Shared\Domain\Exceptions\InvalidMoneyOperation;

/**
 * Immutable monetary amount with ISO 4217 currency code.
 *
 * Always stored as an integer in the smallest currency unit (pence for GBP,
 * cents for USD, yen for JPY). Never use floats for money calculations.
 *
 * Usage:
 *   $price = Money::of(1999, 'GBP');  // £19.99
 *   $total = $price->multiply(3);     // £59.97
 *   $vat   = $total->percentage(20);  // £11.99 (rounded half-up)
 */
final readonly class Money
{
    /**
     * @param  int    $amount    Amount in smallest currency unit (integer cents/pence)
     * @param  string $currency  ISO 4217 currency code (e.g. 'GBP', 'USD', 'EUR')
     */
    private function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException("Currency must be a 3-character ISO 4217 code, got: '{$currency}'");
        }
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, strtoupper($currency));
    }

    public static function zero(string $currency): self
    {
        return new self(0, strtoupper($currency));
    }

    /**
     * Add another Money value. Currencies must match.
     *
     * @throws InvalidMoneyOperation if currencies differ
     */
    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Subtract another Money value. Currencies must match.
     *
     * @throws InvalidMoneyOperation if currencies differ
     */
    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    /**
     * Apply a percentage reduction (e.g. 20 = 20% off).
     * Rounds half-up to the nearest smallest unit.
     */
    public function discountByPercentage(int|float $percentage): self
    {
        $discount = (int) round($this->amount * ($percentage / 100));

        return new self($this->amount - $discount, $this->currency);
    }

    /**
     * Calculate the given percentage of this amount (e.g. for tax calculation).
     * Rounds half-up to the nearest smallest unit.
     */
    public function percentage(int|float $percentage): self
    {
        return new self(
            (int) round($this->amount * ($percentage / 100)),
            $this->currency,
        );
    }

    /**
     * Strip included tax from an inclusive price.
     * e.g. £1.20 inc 20% VAT → £1.00 excl tax
     */
    public function excludeTax(int|float $taxRate): self
    {
        $exclAmount = (int) round($this->amount / (1 + $taxRate / 100));

        return new self($exclAmount, $this->currency);
    }

    /**
     * Convert to another currency using an exchange rate.
     * The rate is units of target currency per 1 unit of this currency.
     */
    public function convertTo(string $targetCurrency, float $rate): self
    {
        return new self(
            (int) round($this->amount * $rate),
            strtoupper($targetCurrency),
        );
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function isGreaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount > $other->amount;
    }

    public function isLessThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount < $other->amount;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    /**
     * Return the minimum of two Money values. Currencies must match.
     */
    public function min(self $other): self
    {
        $this->assertSameCurrency($other);

        return $this->amount <= $other->amount ? $this : $other;
    }

    /**
     * Format as a float for display purposes ONLY.
     * Never use this value for further calculations.
     */
    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function __toString(): string
    {
        return sprintf('%s %d', $this->currency, $this->amount);
    }

    /** @throws InvalidMoneyOperation */
    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidMoneyOperation(
                "Cannot operate on different currencies: {$this->currency} and {$other->currency}"
            );
        }
    }
}
