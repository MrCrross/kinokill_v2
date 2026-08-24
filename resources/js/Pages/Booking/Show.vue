<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { useSeatSelectionStore } from '@/stores/useSeatSelectionStore';
import { Head, useForm, usePage } from '@inertiajs/vue3';
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
    available: boolean;
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

const page = usePage();
const isAuthenticated = computed(() => page.props.auth?.user !== null);

const selection = useSeatSelectionStore();
selection.enterSession(props.session.id);

const seatsById = computed(() => new Map(props.session.seats.map((seat) => [seat.id, seat])));

const selectedSeats = computed(() =>
    selection.selectedSeatIds
        .map((id) => seatsById.value.get(id))
        .filter((seat): seat is Seat => seat !== undefined),
);

const totalPrice = computed(() =>
    selectedSeats.value.reduce((sum, seat) => sum + Number(seat.price), 0).toFixed(2),
);

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

function seatLabel(seat: Seat): string {
    const status = !seat.available ? 'занято' : selection.selectedSeatIds.includes(seat.id) ? 'выбрано' : 'свободно';

    return `Ряд ${seat.row}, место ${seat.column}, ${seat.type === 'vip' ? 'VIP' : 'стандарт'}, ${status}, ${seat.price} ₽`;
}

function onSeatClick(seat: Seat) {
    if (!seat.available) {
        return;
    }

    selection.toggle(seat.id);
}

const form = useForm({
    seat_ids: [] as number[],
    payment_method: 'card_stub',
    guest_name: '',
    guest_email: '',
    guest_phone: '',
});

function submit() {
    form.seat_ids = selection.selectedSeatIds;
    form.post(`/sessions/${props.session.id}/bookings`, {
        onSuccess: () => selection.clear(),
    });
}
</script>

<template>
    <Head :title="`${session.filmTitle} — ${session.hall.name}`" />

    <MainLayout>
        <h1 class="text-2xl font-semibold text-white">{{ session.filmTitle }}</h1>
        <p class="mt-1 text-neutral-400">{{ startsAtFormatted }} · {{ session.hall.name }}</p>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]">
            <div class="rounded-lg bg-black/40 p-6 backdrop-blur">
                <div
                    class="mx-auto mb-8 h-2 max-w-md rounded-full bg-gradient-to-r from-transparent via-neutral-400 to-transparent"
                />

                <div class="grid gap-2 overflow-x-auto" :style="gridStyle">
                    <button
                        v-for="seat in session.seats"
                        :key="seat.id"
                        type="button"
                        :disabled="!seat.available"
                        :style="seatStyle(seat)"
                        :aria-label="seatLabel(seat)"
                        :aria-pressed="selection.selectedSeatIds.includes(seat.id)"
                        class="flex aspect-square items-center justify-center rounded border text-[10px] transition"
                        :class="[
                            !seat.available
                                ? 'cursor-not-allowed border-neutral-700 bg-neutral-800 text-neutral-600'
                                : selection.selectedSeatIds.includes(seat.id)
                                  ? 'border-red-500 bg-red-500/30 text-white'
                                  : seat.type === 'vip'
                                    ? 'border-amber-400/60 bg-amber-400/10 text-neutral-200 hover:bg-amber-400/20'
                                    : 'border-sky-400/40 bg-sky-400/10 text-neutral-200 hover:bg-sky-400/20',
                        ]"
                    >
                        {{ seat.column }}
                    </button>
                </div>

                <div class="mt-8 flex flex-wrap gap-6 text-sm text-neutral-300">
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-4 rounded border border-sky-400/40 bg-sky-400/10" />
                        Стандарт
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-4 rounded border border-amber-400/60 bg-amber-400/10" />
                        VIP
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-4 rounded border border-red-500 bg-red-500/30" />
                        Выбрано
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-4 rounded border border-neutral-700 bg-neutral-800" />
                        Занято
                    </div>
                </div>
            </div>

            <form class="h-fit rounded-lg bg-black/40 p-6 backdrop-blur" @submit.prevent="submit">
                <h2 class="mb-4 text-lg font-medium text-white">Ваш заказ</h2>

                <p v-if="selectedSeats.length === 0" class="text-sm text-neutral-400">
                    Выберите места на схеме зала.
                </p>
                <ul v-else class="mb-4 space-y-1 text-sm text-neutral-200">
                    <li v-for="seat in selectedSeats" :key="seat.id">
                        Ряд {{ seat.row }}, место {{ seat.column }} — {{ seat.price }} ₽
                    </li>
                </ul>

                <p class="mb-4 text-white">Итого: <span class="font-semibold">{{ totalPrice }} ₽</span></p>

                <div v-if="!isAuthenticated" class="mb-4 space-y-3">
                    <div>
                        <label class="mb-1 block text-sm text-neutral-300">Имя</label>
                        <input
                            v-model="form.guest_name"
                            type="text"
                            class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                        />
                        <p v-if="form.errors.guest_name" class="mt-1 text-xs text-red-400">
                            {{ form.errors.guest_name }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-neutral-300">Email</label>
                        <input
                            v-model="form.guest_email"
                            type="email"
                            class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                        />
                        <p v-if="form.errors.guest_email" class="mt-1 text-xs text-red-400">
                            {{ form.errors.guest_email }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-neutral-300">Телефон (необязательно)</label>
                        <input
                            v-model="form.guest_phone"
                            type="tel"
                            class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                        />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm text-neutral-300">Оплата</label>
                    <select
                        v-model="form.payment_method"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                    >
                        <option value="card_stub">Картой онлайн</option>
                        <option value="cash">В кассе кинотеатра</option>
                    </select>
                </div>

                <p v-if="form.errors.seat_ids" class="mb-4 text-sm text-red-400">
                    {{ form.errors.seat_ids }}
                </p>

                <button
                    type="submit"
                    :disabled="selectedSeats.length === 0 || form.processing"
                    class="w-full rounded bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Забронировать
                </button>
            </form>
        </div>
    </MainLayout>
</template>
