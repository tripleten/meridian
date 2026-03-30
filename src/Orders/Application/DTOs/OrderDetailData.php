<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Application\DTOs
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Application\DTOs;

use Meridian\Orders\Domain\Order\OrderStatus;
use Meridian\Orders\Domain\Order\PaymentStatus;
use Spatie\LaravelData\Data;

class OrderDetailData extends Data
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $number,
        public readonly string  $customer_email,
        public readonly string  $customer_name,
        public readonly string  $status,
        public readonly string  $payment_status,
        public readonly int     $grand_total,
        public readonly string  $base_currency,
        public readonly string  $order_currency,
        public readonly ?string $coupon_code,
        public readonly ?string $shipping_method,
        public readonly ?string $shipping_carrier,
        public readonly ?string $placed_at,
        public readonly string  $created_at,
        public readonly int     $item_count,
        // Additional detail fields
        public readonly int     $subtotal,
        public readonly int     $discount_amount,
        public readonly int     $shipping_amount,
        public readonly int     $tax_amount,
        public readonly int     $total_refunded,
        public readonly ?string $payment_method,
        public readonly ?string $invoice_number,
        public readonly array   $shipping_address_snapshot,
        public readonly array   $billing_address_snapshot,
        public readonly ?string $customer_note,
        public readonly array   $customer_snapshot,
    ) {}

    public static function fromModel(object $order): self
    {
        $status = $order->status instanceof OrderStatus
            ? $order->status->value
            : $order->status;

        $paymentStatus = $order->payment_status instanceof PaymentStatus
            ? $order->payment_status->value
            : $order->payment_status;

        $customerName = $order->customer_snapshot['name']
            ?? $order->customer_email;

        $itemCount = $order->order_items_count
            ?? (isset($order->items) ? $order->items->count() : 0);

        return new self(
            id:                         $order->id,
            number:                     $order->number,
            customer_email:             $order->customer_email,
            customer_name:              $customerName,
            status:                     $status,
            payment_status:             $paymentStatus,
            grand_total:                (int) $order->grand_total,
            base_currency:              $order->base_currency,
            order_currency:             $order->order_currency,
            coupon_code:                $order->coupon_code,
            shipping_method:            $order->shipping_method,
            shipping_carrier:           $order->shipping_carrier,
            placed_at:                  $order->placed_at?->toISOString(),
            created_at:                 $order->created_at->toISOString(),
            item_count:                 (int) $itemCount,
            subtotal:                   (int) $order->subtotal,
            discount_amount:            (int) $order->discount_amount,
            shipping_amount:            (int) $order->shipping_amount,
            tax_amount:                 (int) $order->tax_amount,
            total_refunded:             (int) $order->total_refunded,
            payment_method:             $order->payment_method,
            invoice_number:             $order->invoice_number,
            shipping_address_snapshot:  (array) ($order->shipping_address_snapshot ?? []),
            billing_address_snapshot:   (array) ($order->billing_address_snapshot ?? []),
            customer_note:              $order->customer_note,
            customer_snapshot:          (array) ($order->customer_snapshot ?? []),
        );
    }
}
