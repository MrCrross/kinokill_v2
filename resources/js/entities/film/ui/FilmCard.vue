<script setup lang="ts">
import type { FilmListItem } from '@/entities/film/model/types';
import FilmPoster from '@/entities/film/ui/FilmPoster.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    film: FilmListItem;
}>();
</script>

<template>
    <Link
        :href="`/films/${film.id}`"
        class="group flex flex-col overflow-hidden rounded-lg bg-white/5 transition hover:bg-white/10"
    >
        <div class="aspect-[2/3] w-full overflow-hidden bg-neutral-800">
            <FilmPoster
                :poster-url="film.posterUrl"
                :title="film.title"
                img-class="transition group-hover:scale-105"
            />
        </div>
        <div class="p-3">
            <p class="truncate font-medium text-white">{{ film.title }}</p>
            <p class="text-sm text-neutral-400">
                {{ film.country }} · {{ film.ageLimit }}+
            </p>
        </div>
    </Link>
</template>
