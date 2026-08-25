<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-neutral-200 transition duration-150 ease-in-out hover:bg-white/10 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-neutral-950 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
    >
        <slot />
    </button>
</template>
