<script setup lang="ts">
import AuthenticatedLayout from '@/app/layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <Head title="Личный кабинет" />

    <AuthenticatedLayout>
        <template #header>Личный кабинет</template>

        <div
            class="rounded-lg border border-white/10 bg-black/40 p-6 backdrop-blur"
        >
            <p class="text-neutral-300">
                Здравствуйте, {{ user?.name }}! Здесь появится история ваших
                бронирований.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
