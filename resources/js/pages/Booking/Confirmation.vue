<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
import type { BookingDetail } from '@/entities/booking/model/types';
import { formatSessionTime } from '@/shared/lib/formatDateTime';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    booking: BookingDetail;
}>();

const statusLabel = computed(
    () =>
        ({
            pending: 'Ожидает подтверждения',
            confirmed: 'Подтверждено',
            cancelled: 'Отменено',
            expired: 'Время брони истекло',
        })[props.booking.status],
);

function confirm() {
    router.post(`/bookings/${props.booking.id}/confirm`);
}

function cancel() {
    router.post(`/bookings/${props.booking.id}/cancel`);
}
</script>

<template>
    <Head title="Бронирование" />

    <MainLayout>
        <div class="mx-auto max-w-lg rounded-lg bg-black/40 p-6 backdrop-blur">
            <h1 class="text-xl font-semibold text-white">
                {{ booking.filmTitle }}
            </h1>
            <p class="mt-1 text-neutral-400">
                {{ formatSessionTime(booking.startsAt) }} ·
                {{ booking.hallName }}
            </p>

            <p
                class="mt-4 inline-block rounded bg-white/10 px-3 py-1 text-sm text-white"
            >
                {{ statusLabel }}
            </p>

            <ul class="mt-4 space-y-1 text-sm text-neutral-200">
                <li v-for="(seat, index) in booking.seats" :key="index">
                    Ряд {{ seat.row }}, место {{ seat.column }} —
                    {{ seat.price }} ₽
                </li>
            </ul>

            <p class="mt-4 text-white">
                Итого:
                <span class="font-semibold">{{ booking.totalPrice }} ₽</span>
            </p>
            <p class="mt-1 text-sm text-neutral-400">
                Оплата:
                {{
                    booking.paymentMethod === 'cash'
                        ? 'в кассе кинотеатра'
                        : 'картой онлайн'
                }}
            </p>

            <div class="mt-6 flex gap-3">
                <button
                    v-if="booking.canConfirm"
                    type="button"
                    class="rounded bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-500"
                    @click="confirm"
                >
                    Подтвердить оплату
                </button>
                <button
                    v-if="booking.canCancel"
                    type="button"
                    class="rounded border border-white/20 px-4 py-2 font-medium text-white hover:bg-white/10"
                    @click="cancel"
                >
                    Отменить бронь
                </button>
            </div>
        </div>
    </MainLayout>
</template>
