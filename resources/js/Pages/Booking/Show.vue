<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Hall {
    id: number;
    name: string;
    rowsCount: number;
    columnsCount: number;
}

interface Seat {
    id: number;
    row: number;
    column: number;
    type: 'standard' | 'vip';
    price: string;
}

interface SessionDetail {
    id: number;
    filmTitle: string;
    startsAt: string;
    hall: Hall;
    seats: Seat[];
}

const props = defineProps<{
    session: SessionDetail;
}>();

const startsAtFormatted = computed(() =>
    new Date(props.session.startsAt).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }),
);

const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${props.session.hall.columnsCount}, minmax(1.75rem, 1fr))`,
}));

function seatStyle(seat: Seat) {
    return {
        gridRow: seat.row,
        gridColumn: seat.column,
    };
}
</script>

<template>
    <Head :title="`${session.filmTitle} — ${session.hall.name}`" />

    <MainLayout>
        <h1 class="text-2xl font-semibold text-white">{{ session.filmTitle }}</h1>
        <p class="mt-1 text-neutral-400">{{ startsAtFormatted }} · {{ session.hall.name }}</p>

        <div class="mt-8 rounded-lg bg-black/40 p-6 backdrop-blur">
            <div
                class="mx-auto mb-8 h-2 max-w-md rounded-full bg-gradient-to-r from-transparent via-neutral-400 to-transparent"
            />

            <div class="grid gap-2" :style="gridStyle">
                <button
                    v-for="seat in session.seats"
                    :key="seat.id"
                    type="button"
                    disabled
                    :style="seatStyle(seat)"
                    :aria-label="`Ряд ${seat.row}, место ${seat.column}, ${seat.type === 'vip' ? 'VIP' : 'стандарт'}, ${seat.price} ₽`"
                    class="flex aspect-square items-center justify-center rounded border text-[10px] text-neutral-300"
                    :class="
                        seat.type === 'vip'
                            ? 'border-amber-400/60 bg-amber-400/10'
                            : 'border-sky-400/40 bg-sky-400/10'
                    "
                >
                    {{ seat.column }}
                </button>
            </div>

            <div class="mt-8 flex flex-wrap gap-6 text-sm text-neutral-300">
                <div class="flex items-center gap-2">
                    <span class="h-4 w-4 rounded border border-sky-400/40 bg-sky-400/10" />
                    Стандарт — {{ session.seats.find((s) => s.type === 'standard')?.price ?? '—' }} ₽
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-4 w-4 rounded border border-amber-400/60 bg-amber-400/10" />
                    VIP — {{ session.seats.find((s) => s.type === 'vip')?.price ?? '—' }} ₽
                </div>
            </div>

            <p class="mt-6 text-sm text-neutral-500">
                Онлайн-бронирование мест появится на следующем этапе.
            </p>
        </div>
    </MainLayout>
</template>
