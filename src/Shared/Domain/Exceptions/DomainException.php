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

use RuntimeException;

/**
 * Base exception for all domain-layer violations.
 *
 * Catch this in the presentation layer to return a 422/400 response.
 * Never catch inside the domain itself.
 */
class DomainException extends RuntimeException {}
