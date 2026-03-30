<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\IdentityAccess\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\IdentityAccess\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\IdentityAccess\Application\Commands\InviteAdminUserCommand;
use Meridian\IdentityAccess\Application\Commands\InviteAdminUserHandler;
use Meridian\IdentityAccess\Application\Commands\UpdateAdminUserCommand;
use Meridian\IdentityAccess\Application\Commands\UpdateAdminUserHandler;
use Meridian\IdentityAccess\Application\Queries\GetAdminUserHandler;
use Meridian\IdentityAccess\Application\Queries\GetAdminUserQuery;
use Meridian\IdentityAccess\Application\Queries\ListAdminUsersHandler;
use Meridian\IdentityAccess\Application\Queries\ListAdminUsersQuery;
use Meridian\IdentityAccess\Domain\User\UserRole;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class AdminUserController
{
    public function index(Request $request, ListAdminUsersHandler $handler): Response
    {
        $users = $handler->handle(new ListAdminUsersQuery(
            search:  $request->string('search')->trim()->value(),
            role:    $request->string('role')->value(),
            perPage: 20,
        ));

        return Inertia::render('admin/users/index', [
            'users'       => $users,
            'filters'     => $request->only('search', 'role'),
            'adminRoles'  => $this->roleOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/create', [
            'adminRoles' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request, InviteAdminUserHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'  => ['required', 'string', 'in:' . implode(',', UserRole::adminRoleNames())],
        ]);

        try {
            $handler->handle(new InviteAdminUserCommand(
                name:       $validated['name'],
                email:      $validated['email'],
                role:       $validated['role'],
                invitedBy:  $request->user()->id,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user invited. They can set their password via "Forgot password".');
    }

    public function edit(int $user, GetAdminUserHandler $handler): Response
    {
        return Inertia::render('admin/users/edit', [
            'user'       => $handler->handle(new GetAdminUserQuery($user)),
            'adminRoles' => $this->roleOptions(),
        ]);
    }

    public function update(int $user, Request $request, UpdateAdminUserHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user}"],
            'role'  => ['required', 'string', 'in:' . implode(',', UserRole::adminRoleNames())],
        ]);

        try {
            $handler->handle(new UpdateAdminUserCommand(
                userId: $user,
                name:   $validated['name'],
                email:  $validated['email'],
                role:   $validated['role'],
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['role' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(int $user, Request $request): RedirectResponse
    {
        if ($user === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        $targetUser = \App\Models\User::findOrFail($user);
        $targetUser->syncRoles([UserRole::Customer->value]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deactivated (access revoked).');
    }

    /** Returns [value => label] pairs for the role selector. */
    private function roleOptions(): array
    {
        return array_map(
            fn (UserRole $role) => ['value' => $role->value, 'label' => $role->label()],
            UserRole::adminRoles(),
        );
    }
}
