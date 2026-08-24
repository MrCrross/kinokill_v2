<?php

namespace App\Http\Controllers\Admin;

use App\Data\User\UserAdminData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRoleRequest;
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
        $users = User::query()->with('roles')->orderBy('name')->get()
            ->map(fn ($user) => UserAdminData::fromModel($user))
            ->values()
            ->all();

        return Inertia::render('Admin/Users/Index', ['users' => $users]);
    }

    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $this->userRoles->assignRole($request->user(), $user, $request->validated('role'));

        return redirect()->route('admin.users.index')->with('success', 'Роль обновлена.');
    }
}
