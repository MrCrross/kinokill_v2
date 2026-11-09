<script setup lang="ts">
defineProps<{
    currentPage: number;
    lastPage: number;
    total: number;
}>();

const emit = defineEmits<{
    'change-page': [page: number];
}>();
</script>

<template>
    <div
        v-if="lastPage > 1"
        class="mt-4 flex items-center justify-between text-sm text-neutral-400"
    >
        <p>Всего: {{ total }}</p>

        <div class="flex items-center gap-3">
            <button
                type="button"
                class="rounded border border-white/10 bg-white/5 px-3 py-1.5 text-neutral-200 hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="currentPage <= 1"
                @click="emit('change-page', currentPage - 1)"
            >
                Назад
            </button>
            <span>Стр. {{ currentPage }} из {{ lastPage }}</span>
            <button
                type="button"
                class="rounded border border-white/10 bg-white/5 px-3 py-1.5 text-neutral-200 hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="currentPage >= lastPage"
                @click="emit('change-page', currentPage + 1)"
            >
                Вперёд
            </button>
        </div>
    </div>
    <p v-else class="mt-4 text-sm text-neutral-500">Всего: {{ total }}</p>
</template>
