<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { useAdminListFilters } from '@/shared/lib/useAdminListFilters';
import { formatFullDateTime } from '@/shared/lib/formatDateTime';
import Pagination from '@/shared/ui/Pagination.vue';
import Select from '@/shared/ui/Select.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import type { Paginated } from '@/shared/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface SessionAdmin {
    id: number;
    filmTitle: string;
    hallName: string;
    startsAt: string;
}

interface HallOption {
    id: number;
    name: string;
}

const props = defineProps<{
    sessions: Paginated<SessionAdmin>;
    halls: HallOption[];
    filters: { search: string | null; hall_id: number | null };
}>();

const hallOptions = computed(() => [
    { value: '', label: 'Все залы' },
    ...props.halls.map((hall) => ({ value: String(hall.id), label: hall.name })),
]);

const { filters, goToPage } = useAdminListFilters('/admin/sessions', {
    search: props.filters.search ?? '',
    hall_id: props.filters.hall_id ? String(props.filters.hall_id) : '',
});

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

        <div class="mb-4 flex flex-wrap gap-3">
            <TextInput
                v-model="filters.search"
                placeholder="Поиск по фильму…"
                class="w-64"
            />
            <Select
                v-model="filters.hall_id"
                :options="hallOptions"
                placeholder="Все залы"
                class="w-48"
            />
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
                        v-for="session in sessions.data"
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
                    <tr v-if="sessions.data.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                            Ничего не найдено.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination
            :current-page="sessions.currentPage"
            :last-page="sessions.lastPage"
            :total="sessions.total"
            @change-page="goToPage"
        />
    </AdminLayout>
</template>
