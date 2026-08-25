<script setup lang="ts">
withDefaults(
    defineProps<{
        posterUrl: string | null;
        title: string;
        aspect?: string;
        imgClass?: string;
    }>(),
    {
        aspect: 'aspect-[2/3]',
        imgClass: '',
    },
);
</script>

<template>
    <img
        v-if="posterUrl"
        :src="posterUrl"
        :alt="title"
        :class="[aspect, 'w-full object-cover', imgClass]"
    />
    <div
        v-else
        :class="[
            aspect,
            'flex w-full items-center justify-center bg-neutral-800 text-neutral-600',
        ]"
    >
        Нет постера
    </div>
</template>
