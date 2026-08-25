<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Roles/permissions are described here as data (name + Russian label +
     * description of what they grant) — application code never branches on
     * a role's name, only on `->can('permission.name')`.
     */
    public function run(): void
    {
        // findOrCreate()/syncPermissions() read through this cache; a leftover
        // entry from a previous run (e.g. a truncated table) would otherwise
        // make freshly-created permissions invisible within this same run.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            [
                'name' => 'films.manage',
                'label' => 'Управление фильмами',
                'description' => 'Добавление, редактирование и удаление фильмов в каталоге.',
            ],
            [
                'name' => 'sessions.manage',
                'label' => 'Управление сеансами',
                'description' => 'Планирование и отмена сеансов показа фильмов в залах.',
            ],
            [
                'name' => 'halls.manage',
                'label' => 'Управление залами',
                'description' => 'Добавление и редактирование залов кинотеатра и схемы мест в них.',
            ],
            [
                'name' => 'bookings.manage-any',
                'label' => 'Управление чужими бронями',
                'description' => 'Просмотр и подтверждение броней, оформленных другими покупателями (например, оплата наличными в кассе).',
            ],
            [
                'name' => 'users.manage-roles',
                'label' => 'Назначение ролей пользователям',
                'description' => 'Изменение роли (набора прав) у любого зарегистрированного пользователя.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                ['label' => $permission['label'], 'description' => $permission['description']],
            );
        }

        $roles = [
            [
                'name' => 'admin',
                'label' => 'Администратор',
                'description' => 'Полный доступ ко всем разделам администрирования кинотеатра.',
                'permissions' => [
                    'films.manage',
                    'sessions.manage',
                    'halls.manage',
                    'bookings.manage-any',
                    'users.manage-roles',
                ],
            ],
            [
                'name' => 'manager',
                'label' => 'Менеджер',
                'description' => 'Оперативное управление сеансами и бронями, без доступа к каталогу фильмов, залам и пользователям.',
                'permissions' => [
                    'sessions.manage',
                    'bookings.manage-any',
                ],
            ],
            [
                'name' => 'customer',
                'label' => 'Покупатель',
                'description' => 'Обычный зарегистрированный посетитель сайта: бронирует билеты, доступа к администрированию не имеет.',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $role) {
            $model = Role::query()->updateOrCreate(
                ['name' => $role['name']],
                ['label' => $role['label'], 'description' => $role['description']],
            );

            $model->syncPermissions($role['permissions']);
        }
    }
}
