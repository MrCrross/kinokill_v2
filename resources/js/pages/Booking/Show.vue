<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
import type { Seat } from '@/entities/seat/model/types';
import type { SessionDetail } from '@/entities/session/model/types';
import CreateBookingForm from '@/features/create-booking/ui/CreateBookingForm.vue';
import { useSeatSelectionStore } from '@/features/select-seat/model/useSeatSelectionStore';
import { formatSessionTime } from '@/shared/lib/formatDateTime';
import SeatMap from '@/widgets/seat-map/ui/SeatMap.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    session: SessionDetail;
}>();

const selection = useSeatSelectionStore();
selection.enterSession(props.session.id);

const seatsById = computed(
    () => new Map(props.session.seats.map((seat) => [seat.id, seat])),
);

const selectedSeats = computed(() =>
    selection.selectedSeatIds
        .map((id) => seatsById.value.get(id))
        .filter((seat): seat is Seat => seat !== undefined),
);
</script>

<template>
    <Head :title="`${session.filmTitle} — ${session.hall.name}`" />

    <MainLayout>
        <h1
            class="font-display text-3xl font-semibold tracking-wide text-white uppercase"
        >
            {{ session.filmTitle }}
        </h1>
        <p class="mt-1 text-neutral-400">
            {{ formatSessionTime(session.startsAt) }} · {{ session.hall.name }}
        </p>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]">
            <SeatMap
                :hall="session.hall"
                :seats="session.seats"
                :selected-seat-ids="selection.selectedSeatIds"
                @toggle-seat="selection.toggle"
            />

            <CreateBookingForm
                :session-id="session.id"
                :selected-seats="selectedSeats"
                @submitted="selection.clear"
            />
        </div>
    </MainLayout>
</template>
