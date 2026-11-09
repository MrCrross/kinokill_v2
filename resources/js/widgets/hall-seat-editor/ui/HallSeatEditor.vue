<script setup lang="ts">
import { computed } from 'vue';

export type SeatState = 'standard' | 'vip' | null;

const props = withDefaults(
    defineProps<{
        rows: number;
        columns: number;
        modelValue: Record<string, SeatState>;
        readonly?: boolean;
    }>(),
    {
        readonly: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, SeatState>];
}>();

const grid = computed(() => {
    const cells: { key: string; row: number; column: number; state: SeatState }[] = [];

    for (let row = 1; row <= props.rows; row++) {
        for (let column = 1; column <= props.columns; column++) {
            const key = `${row}-${column}`;
            cells.push({ key, row, column, state: props.modelValue[key] ?? null });
        }
    }

    return cells;
});

const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${props.columns}, minmax(1.75rem, 1fr))`,
}));

const nextState: Record<'gap' | 'standard' | 'vip', SeatState> = {
    gap: 'standard',
    standard: 'vip',
    vip: null,
};

function toggle(key: string) {
    if (props.readonly) return;

    const current = props.modelValue[key] ?? null;
    emit('update:modelValue', {
        ...props.modelValue,
        [key]: nextState[current ?? 'gap'],
    });
}

function labelFor(row: number, column: number, state: SeatState): string {
    const kind =
        state === 'vip' ? 'VIP' : state === 'standard' ? 'стандарт' : 'проход, нет места';

    return `Ряд ${row}, место ${column}: ${kind}`;
}
</script>

<template>
    <div class="overflow-x-auto rounded-lg bg-black/40 p-4">
        <div class="grid gap-1.5" :style="gridStyle">
            <button
                v-for="cell in grid"
                :key="cell.key"
                type="button"
                :disabled="readonly"
                class="flex aspect-square items-center justify-center rounded border text-[10px] transition"
                :class="[
                    cell.state === 'vip'
                        ? 'border-amber-400/60 bg-amber-400/10 text-neutral-200'
                        : cell.state === 'standard'
                          ? 'border-sky-400/40 bg-sky-400/10 text-neutral-200'
                          : 'border-dashed border-white/10 bg-transparent text-neutral-700',
                    !readonly && 'cursor-pointer hover:brightness-125',
                ]"
                :aria-label="labelFor(cell.row, cell.column, cell.state)"
                @click="toggle(cell.key)"
            >
                {{ cell.state ? cell.column : '' }}
            </button>
        </div>

        <div class="mt-4 flex flex-wrap gap-4 text-xs text-neutral-400">
            <div class="flex items-center gap-2">
                <span
                    class="h-3 w-3 rounded border border-sky-400/40 bg-sky-400/10"
                />
                Стандарт
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="h-3 w-3 rounded border border-amber-400/60 bg-amber-400/10"
                />
                VIP
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded border border-dashed border-white/10" />
                Проход / нет места
            </div>
        </div>
    </div>
</template>
