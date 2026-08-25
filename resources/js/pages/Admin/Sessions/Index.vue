<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { formatFullDateTime } from '@/shared/lib/formatDateTime';
import { Head, Link, router } from '@inertiajs/vue3';

interface SessionAdmin {
    id: number;
    filmTitle: string;
    hallName: string;
    startsAt: string;
}

defineProps<{
    sessions: SessionAdmin[];
}>();

function destroy(session: SessionAdmin) {
    if (
        confirm(
            `Удалить сеанс «${session.filmTitle}» (${formatFullDateTime(session.startsAt)})?`,
        )
    ) {
        router.delete(`/admin/sessions/${session.id}`);
    }
}
</script>

<template>
    <Head title="Сеансы — админка" />

    <AdminLayout>
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">Сеансы</h1>
            <Link
                href="/admin/sessions/create"
                class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-500"
            >
                Добавить сеанс
            </Link>
        </div>

        <div class="overflow-x-auto rounded-lg bg-black/40">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="text-neutral-400">
                    <tr>
                        <th class="px-4 py-3">Фильм</th>
                        <th class="px-4 py-3">Зал</th>
                        <th class="px-4 py-3">Дата и время</th>
                        <th class="px-4 py-3" />
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="session in sessions"
                        :key="session.id"
                        class="border-t border-white/10"
                    >
                        <td class="px-4 py-3">{{ session.filmTitle }}</td>
                        <td class="px-4 py-3">{{ session.hallName }}</td>
                        <td class="px-4 py-3">
                            {{ formatFullDateTime(session.startsAt) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                type="button"
                                class="text-red-400 hover:underline"
                                @click="destroy(session)"
                            >
                                Удалить
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
