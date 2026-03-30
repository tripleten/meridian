<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Shared\Domain\Exceptions
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Shared\Domain\Exceptions;

/**
 * Thrown when an illegal Money operation is attempted,
 * such as adding two different currencies together.
 */
final class InvalidMoneyOperation extends DomainException {}
