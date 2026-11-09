<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import type { FilmListItem, Genre } from '@/entities/film/model/types';
import { useAdminListFilters } from '@/shared/lib/useAdminListFilters';
import Pagination from '@/shared/ui/Pagination.vue';
import Select from '@/shared/ui/Select.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import type { Paginated } from '@/shared/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    films: Paginated<FilmListItem>;
    genres: Genre[];
    filters: { search: string | null; genre_id: number | null };
}>();

const genreOptions = computed(() => [
    { value: '', label: 'Все жанры' },
    ...props.genres.map((genre) => ({ value: String(genre.id), label: genre.name })),
]);

const { filters, goToPage } = useAdminListFilters('/admin/films', {
    search: props.filters.search ?? '',
    genre_id: props.filters.genre_id ? String(props.filters.genre_id) : '',
});

function destroy(film: FilmListItem) {
    if (confirm(`Удалить фильм «${film.title}»?`)) {
        router.delete(`/admin/films/${film.id}`);
    }
}
</script>

<template>
    <Head title="Фильмы — админка" />

    <AdminLayout>
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">Фильмы</h1>
            <Link
                href="/admin/films/create"
                class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-500"
            >
                Добавить фильм
            </Link>
        </div>

        <div class="mb-4 flex flex-wrap gap-3">
            <TextInput
                v-model="filters.search"
                placeholder="Поиск по названию…"
                class="w-64"
            />
            <Select
                v-model="filters.genre_id"
                :options="genreOptions"
                placeholder="Все жанры"
                class="w-48"
            />
        </div>

        <div class="overflow-x-auto rounded-lg bg-black/40">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="text-neutral-400">
                    <tr>
                        <th class="px-4 py-3">Название</th>
                        <th class="px-4 py-3">Страна</th>
                        <th class="px-4 py-3">Возраст</th>
                        <th class="px-4 py-3" />
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="film in films.data"
                        :key="film.id"
                        class="border-t border-white/10"
                    >
                        <td class="px-4 py-3">{{ film.title }}</td>
                        <td class="px-4 py-3">{{ film.country }}</td>
                        <td class="px-4 py-3">{{ film.ageLimit }}+</td>
                        <td class="px-4 py-3 text-right">
                            <Link
                                :href="`/admin/films/${film.id}/edit`"
                                class="mr-3 text-sky-400 hover:underline"
                            >
                                Изменить
                            </Link>
                            <button
                                type="button"
                                class="text-red-400 hover:underline"
                                @click="destroy(film)"
                            >
                                Удалить
                            </button>
                        </td>
                    </tr>
                    <tr v-if="films.data.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                            Ничего не найдено.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination
            :current-page="films.currentPage"
            :last-page="films.lastPage"
            :total="films.total"
            @change-page="goToPage"
        />
    </AdminLayout>
</template>
