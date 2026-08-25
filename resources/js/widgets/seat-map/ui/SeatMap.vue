<script setup lang="ts">
import type { Seat } from '@/entities/seat/model/types';
import SeatCell from '@/entities/seat/ui/SeatCell.vue';
import type { Hall } from '@/entities/session/model/types';
import { computed } from 'vue';

const props = defineProps<{
    hall: Hall;
    seats: Seat[];
    selectedSeatIds: number[];
}>();

const emit = defineEmits<{
    'toggle-seat': [seatId: number];
}>();

const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${props.hall.columnsCount}, minmax(1.75rem, 1fr))`,
}));

function isSelected(seatId: number): boolean {
    return props.selectedSeatIds.includes(seatId);
}
</script>

<template>
    <div class="rounded-lg bg-black/40 p-6 backdrop-blur">
        <div
            class="mx-auto mb-8 h-2 max-w-md rounded-full bg-gradient-to-r from-transparent via-neutral-400 to-transparent"
        />

        <div class="grid gap-2 overflow-x-auto" :style="gridStyle">
            <SeatCell
                v-for="seat in seats"
                :key="seat.id"
                :seat="seat"
                :selected="isSelected(seat.id)"
                @toggle="emit('toggle-seat', $event)"
            />
        </div>

        <div class="mt-8 flex flex-wrap gap-6 text-sm text-neutral-300">
            <div class="flex items-center gap-2">
                <span
                    class="h-4 w-4 rounded border border-sky-400/40 bg-sky-400/10"
                />
                Стандарт
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="h-4 w-4 rounded border border-amber-400/60 bg-amber-400/10"
                />
                VIP
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="h-4 w-4 rounded border border-red-500 bg-red-500/30"
                />
                Выбрано
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="h-4 w-4 rounded border border-neutral-700 bg-neutral-800"
                />
                Занято
            </div>
        </div>
    </div>
</template>
