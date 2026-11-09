<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { useAdminListFilters } from '@/shared/lib/useAdminListFilters';
import Pagination from '@/shared/ui/Pagination.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import type { Paginated } from '@/shared/types';
import { Head, Link, router } from '@inertiajs/vue3';

interface Hall {
    id: number;
    name: string;
    rowsCount: number;
    columnsCount: number;
    priceStandard: string;
    priceVip: string;
}

const props = defineProps<{
    halls: Paginated<Hall>;
    filters: { search: string | null };
}>();

const { filters, goToPage } = useAdminListFilters('/admin/halls', {
    search: props.filters.search ?? '',
});

function destroy(hall: Hall) {
    if (confirm(`Удалить зал «${hall.name}»?`)) {
        router.delete(`/admin/halls/${hall.id}`);
    }
}
</script>

<template>
    <Head title="Залы — админка" />

    <AdminLayout>
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">Залы</h1>
            <Link
                href="/admin/halls/create"
                class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-500"
            >
                Добавить зал
            </Link>
        </div>

        <div class="mb-4">
            <TextInput
                v-model="filters.search"
                placeholder="Поиск по названию…"
                class="w-64"
            />
        </div>

        <div class="overflow-x-auto rounded-lg bg-black/40">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="text-neutral-400">
                    <tr>
                        <th class="px-4 py-3">Название</th>
                        <th class="px-4 py-3">Рядов × Мест</th>
                        <th class="px-4 py-3">Цены</th>
                        <th class="px-4 py-3" />
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="hall in halls.data"
                        :key="hall.id"
                        class="border-t border-white/10"
                    >
                        <td class="px-4 py-3">{{ hall.name }}</td>
                        <td class="px-4 py-3">
                            {{ hall.rowsCount }} × {{ hall.columnsCount }}
                        </td>
                        <td class="px-4 py-3">
                            {{ hall.priceStandard }} / {{ hall.priceVip }} ₽
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Link
                                :href="`/admin/halls/${hall.id}/edit`"
                                class="mr-3 text-sky-400 hover:underline"
                            >
                                Изменить
                            </Link>
                            <button
                                type="button"
                                class="text-red-400 hover:underline"
                                @click="destroy(hall)"
                            >
                                Удалить
                            </button>
                        </td>
                    </tr>
                    <tr v-if="halls.data.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                            Ничего не найдено.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination
            :current-page="halls.currentPage"
            :last-page="halls.lastPage"
            :total="halls.total"
            @change-page="goToPage"
        />
    </AdminLayout>
</template>
