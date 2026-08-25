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
            'flex w-full flex-col items-center justify-center gap-2 bg-gradient-to-b from-neutral-800 to-neutral-900 text-neutral-600',
        ]"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            class="h-10 w-10"
        >
            <rect x="2.5" y="4.5" width="19" height="15" rx="1.5" />
            <path d="M2.5 8.5h19M2.5 15.5h19M8 4.5v15M16 4.5v15" />
        </svg>
        <span class="text-xs">Нет постера</span>
    </div>
</template>
