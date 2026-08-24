<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Link, Head } from '@inertiajs/vue3';

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
</script>

<template>
    <Head title="Фильмы" />

    <MainLayout>
        <h1 class="mb-6 text-2xl font-semibold text-white">Сейчас в прокате</h1>

        <p v-if="films.length === 0" class="text-neutral-400">Фильмов пока нет.</p>

        <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            <Link
                v-for="film in films"
                :key="film.id"
                :href="`/films/${film.id}`"
                class="group flex flex-col overflow-hidden rounded-lg bg-white/5 transition hover:bg-white/10"
            >
                <div class="aspect-[2/3] w-full overflow-hidden bg-neutral-800">
                    <img
                        v-if="film.posterUrl"
                        :src="film.posterUrl"
                        :alt="film.title"
                        class="h-full w-full object-cover transition group-hover:scale-105"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center text-neutral-600">
                        Нет постера
                    </div>
                </div>
                <div class="p-3">
                    <p class="truncate font-medium text-white">{{ film.title }}</p>
                    <p class="text-sm text-neutral-400">{{ film.country }} · {{ film.ageLimit }}+</p>
                </div>
            </Link>
        </div>
    </MainLayout>
</template>
