<script setup lang="ts">
import type { ScheduleDay } from '@/entities/session/model/types';
import { formatScheduleDayLabel } from '@/shared/lib/formatDateTime';
import { router } from '@inertiajs/vue3';

defineProps<{
    days: ScheduleDay[];
}>();

function select(date: string) {
    router.get(
        '/sessions',
        { date },
        { preserveScroll: true, preserveState: true },
    );
}
</script>

<template>
    <div class="mb-6 flex gap-2 overflow-x-auto pb-1" role="tablist">
        <button
            v-for="(day, index) in days"
            :key="day.date"
            type="button"
            role="tab"
            :aria-selected="day.isSelected"
            class="shrink-0 rounded-full border px-4 py-2 text-sm font-medium transition"
            :class="
                day.isSelected
                    ? 'border-red-500 bg-red-600 text-white'
                    : 'border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10'
            "
            @click="select(day.date)"
        >
            {{ formatScheduleDayLabel(day.date, index) }}
        </button>
    </div>
</template>
