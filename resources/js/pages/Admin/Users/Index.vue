<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';

interface AdminUser {
    id: number;
    name: string;
    email: string;
    role: string | null;
}

defineProps<{
    users: AdminUser[];
}>();

function updateRole(user: AdminUser, role: string) {
    router.put(`/admin/users/${user.id}/role`, { role });
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
                                :value="user.role ?? ''"
                                class="rounded border border-white/10 bg-white/5 px-2 py-1 text-white"
                                @change="
                                    updateRole(
                                        user,
                                        ($event.target as HTMLSelectElement)
                                            .value,
                                    )
                                "
                            >
                                <option value="" disabled>Без роли</option>
                                <option value="admin">admin</option>
                                <option value="manager">manager</option>
                                <option value="customer">customer</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
