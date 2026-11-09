<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { useAdminListFilters } from '@/shared/lib/useAdminListFilters';
import Pagination from '@/shared/ui/Pagination.vue';
import Select from '@/shared/ui/Select.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import type { Paginated } from '@/shared/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface RolePermission {
    id: number;
    name: string;
    label: string | null;
    description: string | null;
}

interface AdminRole {
    id: number;
    name: string;
    label: string | null;
    description: string | null;
    permissions: RolePermission[];
}

interface AdminUser {
    id: number;
    name: string;
    email: string;
    role: AdminRole | null;
}

const props = defineProps<{
    users: Paginated<AdminUser>;
    roles: AdminRole[];
    filters: { search: string | null; role_id: number | null };
}>();

const roleOptions = computed(() =>
    props.roles.map((role) => ({
        value: String(role.id),
        label: role.label ?? role.name,
        description: role.description,
    })),
);

const roleFilterOptions = computed(() => [
    { value: '', label: 'Все роли' },
    ...roleOptions.value,
]);

const { filters, goToPage } = useAdminListFilters('/admin/users', {
    search: props.filters.search ?? '',
    role_id: props.filters.role_id ? String(props.filters.role_id) : '',
});

function updateRole(user: AdminUser, roleId: string) {
    router.put(`/admin/users/${user.id}/role`, { role_id: Number(roleId) });
}
</script>

<template>
    <Head title="Пользователи — админка" />

    <AdminLayout>
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">Пользователи</h1>
            <Link
                href="/admin/users/create"
                class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-500"
            >
                Добавить пользователя
            </Link>
        </div>

        <div class="mb-4 flex flex-wrap gap-3">
            <TextInput
                v-model="filters.search"
                placeholder="Поиск по имени или email…"
                class="w-64"
            />
            <Select
                v-model="filters.role_id"
                :options="roleFilterOptions"
                placeholder="Все роли"
                class="w-48"
            />
        </div>

        <div class="overflow-x-auto rounded-lg bg-black/40">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="text-neutral-400">
                    <tr>
                        <th class="px-4 py-3">Имя</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Роль</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-t border-white/10"
                    >
                        <td class="px-4 py-3">{{ user.name }}</td>
                        <td class="px-4 py-3">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <Select
                                class="max-w-48"
                                :model-value="
                                    user.role ? String(user.role.id) : undefined
                                "
                                :options="roleOptions"
                                placeholder="Без роли"
                                @update:model-value="
                                    (value) => value && updateRole(user, value)
                                "
                            />
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="3" class="px-4 py-6 text-center text-neutral-500">
                            Ничего не найдено.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination
            :current-page="users.currentPage"
            :last-page="users.lastPage"
            :total="users.total"
            @change-page="goToPage"
        />

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div
                v-for="role in roles"
                :key="role.id"
                class="rounded-lg bg-black/40 p-4"
            >
                <h2 class="font-medium text-white">
                    {{ role.label ?? role.name }}
                </h2>
                <p
                    v-if="role.description"
                    class="mt-1 text-xs text-neutral-400"
                >
                    {{ role.description }}
                </p>
                <ul class="mt-3 space-y-1 text-xs text-neutral-300">
                    <li
                        v-if="role.permissions.length === 0"
                        class="text-neutral-500"
                    >
                        Без прав администрирования
                    </li>
                    <li
                        v-for="permission in role.permissions"
                        :key="permission.id"
                    >
                        {{ permission.label ?? permission.name }}
                    </li>
                </ul>
            </div>
        </div>
    </AdminLayout>
</template>
