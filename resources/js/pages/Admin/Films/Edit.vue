<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import type { Film, Genre } from '@/entities/film/model/types';
import FilmForm from '@/features/manage-film/ui/FilmForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    film: Film;
    genres: Genre[];
    countries: string[];
    producers: string[];
    ageRatings: number[];
}>();
</script>

<template>
    <Head :title="`Редактировать «${film.title}»`" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">
            Редактировать «{{ film.title }}»
        </h1>

        <FilmForm
            :action="`/admin/films/${film.id}`"
            method="put"
            :film="film"
            :genres="genres"
            :countries="countries"
            :producers="producers"
            :age-ratings="ageRatings"
        />
    </AdminLayout>
</template>
