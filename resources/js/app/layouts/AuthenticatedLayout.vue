<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
</script>

<template>
    <MainLayout>
        <h1 v-if="$slots.header" class="mb-6 text-xl font-semibold text-white">
            <slot name="header" />
        </h1>

        <slot />
    </MainLayout>
</template>
