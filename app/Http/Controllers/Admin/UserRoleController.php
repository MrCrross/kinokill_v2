<?php

namespace App\Http\Controllers\Admin;

use App\Data\Role\RoleData;
use App\Data\User\UserAdminData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserRoleController extends Controller
{
    public function __construct(
        private readonly UserRoleService $userRoles,
    ) {}

    public function index(): Response
    {
        $users = User::query()->with('roles.permissions')->orderBy('name')->get()
            ->map(fn ($user) => UserAdminData::fromModel($user))
            ->values()
            ->all();

        $roles = Role::query()->with('permissions')->orderBy('name')->get()
            ->map(fn ($role) => RoleData::fromModel($role))
            ->values()
            ->all();

        return Inertia::render('Admin/Users/Index', ['users' => $users, 'roles' => $roles]);
    }

    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $role = Role::findOrFail($request->validated('role_id'));

        $this->userRoles->assignRole($request->user(), $user, $role);

        return redirect()->route('admin.users.index')->with('success', 'Роль обновлена.');
    }
}
