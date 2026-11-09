<?php

namespace App\Http\Controllers\Admin;

use App\Data\Role\RoleData;
use App\Data\User\StoreUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserRoleController extends Controller
{
    public function __construct(
        private readonly UserRoleService $userRoles,
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value() ?: null;
        $roleId = $request->integer('role_id') ?: null;

        $roles = Role::query()->with('permissions')->orderBy('name')->get()
            ->map(fn ($role) => RoleData::fromModel($role))
            ->values()
            ->all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $this->userRoles->paginateUsers($search, $roleId),
            'roles' => $roles,
            'filters' => ['search' => $search, 'role_id' => $roleId],
        ]);
    }

    public function create(): Response
    {
        $roles = Role::query()->orderBy('name')->get()
            ->map(fn ($role) => RoleData::fromModel($role))
            ->values()
            ->all();

        return Inertia::render('Admin/Users/Create', ['roles' => $roles]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userRoles->inviteUser(new StoreUserData(
            name: $request->validated('name'),
            email: $request->validated('email'),
            roleId: $request->validated('role_id') ? (int) $request->validated('role_id') : null,
        ));

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь добавлен, письмо со ссылкой для входа отправлено.');
    }

    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $role = Role::findOrFail($request->validated('role_id'));

        $this->userRoles->assignRole($request->user(), $user, $role);

        return redirect()->route('admin.users.index')->with('success', 'Роль обновлена.');
    }
}
