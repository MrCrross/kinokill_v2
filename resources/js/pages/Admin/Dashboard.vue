<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Админка" />

    <AdminLayout>
        <h1 class="text-xl font-semibold text-white">Панель управления</h1>
        <p class="mt-2 text-neutral-400">
            Выберите раздел слева: фильмы, залы, сеансы или пользователи.
        </p>
    </AdminLayout>
</template>
