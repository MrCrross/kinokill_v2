<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
import type { FilmListItem } from '@/entities/film/model/types';
import FilmCatalogGrid from '@/widgets/film-catalog-grid/ui/FilmCatalogGrid.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    films: FilmListItem[];
}>();
</script>

<template>
    <Head title="Фильмы" />

    <MainLayout>
        <h1
            class="font-display mb-6 text-3xl font-semibold tracking-wide text-white uppercase"
        >
            Сейчас в прокате
        </h1>

        <FilmCatalogGrid :films="films" />
    </MainLayout>
</template>
