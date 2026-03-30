<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Presentation\Middleware
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Presentation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Meridian\IdentityAccess\Domain\User\UserRole;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasAnyRole(UserRole::adminRoleNames())) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
