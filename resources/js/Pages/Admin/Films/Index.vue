<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface FilmListItem {
    id: number;
    title: string;
    country: string;
    ageLimit: number;
    posterUrl: string | null;
}

defineProps<{
    films: FilmListItem[];
}>();

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
            <Link href="/admin/films/create" class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-500">
                Добавить фильм
            </Link>
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
                    <tr v-for="film in films" :key="film.id" class="border-t border-white/10">
                        <td class="px-4 py-3">{{ film.title }}</td>
                        <td class="px-4 py-3">{{ film.country }}</td>
                        <td class="px-4 py-3">{{ film.ageLimit }}+</td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="`/admin/films/${film.id}/edit`" class="mr-3 text-sky-400 hover:underline">
                                Изменить
                            </Link>
                            <button type="button" class="text-red-400 hover:underline" @click="destroy(film)">
                                Удалить
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
