<script setup lang="ts">
import type { FilmListItem } from '@/entities/film/model/types';
import FilmCard from '@/entities/film/ui/FilmCard.vue';

defineProps<{
    films: FilmListItem[];
}>();
</script>

<template>
    <p v-if="films.length === 0" class="text-neutral-400">Фильмов пока нет.</p>

    <div
        v-else
        class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
    >
        <FilmCard v-for="film in films" :key="film.id" :film="film" />
    </div>
</template>
