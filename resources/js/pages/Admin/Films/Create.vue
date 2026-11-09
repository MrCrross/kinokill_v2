<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import type { Genre } from '@/entities/film/model/types';
import FilmForm from '@/features/manage-film/ui/FilmForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    genres: Genre[];
    countries: string[];
    producers: string[];
    ageRatings: number[];
}>();
</script>

<template>
    <Head title="Добавить фильм" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Добавить фильм</h1>

        <FilmForm
            action="/admin/films"
            :genres="genres"
            :countries="countries"
            :producers="producers"
            :age-ratings="ageRatings"
        />
    </AdminLayout>
</template>
