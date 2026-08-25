<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';

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

defineProps<{
    users: AdminUser[];
    roles: AdminRole[];
}>();

function updateRole(user: AdminUser, roleId: string) {
    router.put(`/admin/users/${user.id}/role`, { role_id: Number(roleId) });
}
</script>

<template>
    <Head title="Пользователи — админка" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Пользователи</h1>

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
                        v-for="user in users"
                        :key="user.id"
                        class="border-t border-white/10"
                    >
                        <td class="px-4 py-3">{{ user.name }}</td>
                        <td class="px-4 py-3">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <select
                                :value="user.role?.id ?? ''"
                                class="rounded border border-white/10 bg-white/5 px-2 py-1 text-white"
                                :title="user.role?.description ?? ''"
                                @change="
                                    updateRole(
                                        user,
                                        ($event.target as HTMLSelectElement)
                                            .value,
                                    )
                                "
                            >
                                <option value="" disabled>Без роли</option>
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.id"
                                    :title="role.description ?? ''"
                                >
                                    {{ role.label ?? role.name }}
                                </option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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
