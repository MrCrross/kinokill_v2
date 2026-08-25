<script setup lang="ts">
import type { Seat } from '@/entities/seat/model/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    sessionId: number;
    selectedSeats: Seat[];
}>();

const emit = defineEmits<{
    submitted: [];
}>();

const isAuthenticated = computed(() => usePage().props.auth?.user !== null);

const totalPrice = computed(() =>
    props.selectedSeats
        .reduce((sum, seat) => sum + Number(seat.price), 0)
        .toFixed(2),
);

const form = useForm({
    seat_ids: [] as number[],
    payment_method: 'card_stub',
    guest_name: '',
    guest_email: '',
    guest_phone: '',
});

function submit() {
    form.seat_ids = props.selectedSeats.map((seat) => seat.id);
    form.post(`/sessions/${props.sessionId}/bookings`, {
        onSuccess: () => emit('submitted'),
    });
}
</script>

<template>
    <form
        class="h-fit rounded-lg bg-black/40 p-6 backdrop-blur"
        @submit.prevent="submit"
    >
        <h2 class="mb-4 text-lg font-medium text-white">Ваш заказ</h2>

        <p v-if="selectedSeats.length === 0" class="text-sm text-neutral-400">
            Выберите места на схеме зала.
        </p>
        <ul v-else class="mb-4 space-y-1 text-sm text-neutral-200">
            <li v-for="seat in selectedSeats" :key="seat.id">
                Ряд {{ seat.row }}, место {{ seat.column }} — {{ seat.price }} ₽
            </li>
        </ul>

        <p class="mb-4 text-white">
            Итого: <span class="font-semibold">{{ totalPrice }} ₽</span>
        </p>

        <div v-if="!isAuthenticated" class="mb-4 space-y-3">
            <div>
                <label class="mb-1 block text-sm text-neutral-300">Имя</label>
                <input
                    v-model="form.guest_name"
                    type="text"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                />
                <p
                    v-if="form.errors.guest_name"
                    class="mt-1 text-xs text-red-400"
                >
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
                <p
                    v-if="form.errors.guest_email"
                    class="mt-1 text-xs text-red-400"
                >
                    {{ form.errors.guest_email }}
                </p>
            </div>
            <div>
                <label class="mb-1 block text-sm text-neutral-300"
                    >Телефон (необязательно)</label
                >
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
</template>
