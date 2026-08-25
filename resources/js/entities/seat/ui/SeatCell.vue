<script setup lang="ts">
import type { Seat } from '@/entities/seat/model/types';
import { computed } from 'vue';

const props = defineProps<{
    seat: Seat;
    selected: boolean;
}>();

const emit = defineEmits<{
    toggle: [seatId: number];
}>();

const status = computed(() => {
    if (!props.seat.available) return 'занято';

    return props.selected ? 'выбрано' : 'свободно';
});

const label = computed(
    () =>
        `Ряд ${props.seat.row}, место ${props.seat.column}, ${props.seat.type === 'vip' ? 'VIP' : 'стандарт'}, ${status.value}, ${props.seat.price} ₽`,
);

function onClick() {
    if (props.seat.available) {
        emit('toggle', props.seat.id);
    }
}
</script>

<template>
    <button
        type="button"
        :disabled="!seat.available"
        :style="{ gridRow: seat.row, gridColumn: seat.column }"
        :aria-label="label"
        :aria-pressed="selected"
        class="flex aspect-square items-center justify-center rounded border text-[10px] transition"
        :class="[
            !seat.available
                ? 'cursor-not-allowed border-neutral-700 bg-neutral-800 text-neutral-600'
                : selected
                  ? 'border-red-500 bg-red-500/30 text-white'
                  : seat.type === 'vip'
                    ? 'border-amber-400/60 bg-amber-400/10 text-neutral-200 hover:bg-amber-400/20'
                    : 'border-sky-400/40 bg-sky-400/10 text-neutral-200 hover:bg-sky-400/20',
        ]"
        @click="onClick"
    >
        {{ seat.column }}
    </button>
</template>
