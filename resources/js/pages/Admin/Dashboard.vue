<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { formatFullDateTime } from '@/shared/lib/formatDateTime';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface UpcomingSession {
    id: number;
    filmTitle: string;
    hallName: string;
    startsAt: string;
    bookedSeats: number;
    capacity: number;
}

interface DashboardStats {
    filmsTotal: number;
    hallsTotal: number;
    usersTotal: number;
    upcomingSessionsCount: number;
    revenueLast30Days: string;
    averageOccupancyNext7Days: number;
    upcomingSessions: UpcomingSession[];
}

const props = defineProps<{
    stats: DashboardStats;
}>();

const tiles = computed(() => [
    { label: 'Фильмов в каталоге', value: props.stats.filmsTotal },
    { label: 'Залов', value: props.stats.hallsTotal },
    { label: 'Пользователей', value: props.stats.usersTotal },
    { label: 'Ближайших сеансов', value: props.stats.upcomingSessionsCount },
    { label: 'Выручка за 30 дней', value: `${props.stats.revenueLast30Days} ₽` },
    {
        label: 'Заполняемость залов (7 дней)',
        value: `${props.stats.averageOccupancyNext7Days}%`,
    },
]);

function occupancyOf(session: UpcomingSession): number {
    return session.capacity > 0
        ? Math.round((session.bookedSeats / session.capacity) * 100)
        : 0;
}
</script>

<template>
    <Head title="Админка" />

    <AdminLayout>
        <h1 class="mb-6 text-xl font-semibold text-white">Панель управления</h1>

        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="tile in tiles"
                :key="tile.label"
                class="rounded-lg bg-black/40 p-4"
            >
                <p class="text-2xl font-semibold text-white">{{ tile.value }}</p>
                <p class="mt-1 text-sm text-neutral-400">{{ tile.label }}</p>
            </div>
        </div>

        <h2 class="mb-3 text-lg font-medium text-white">Ближайшие сеансы</h2>

        <div class="overflow-x-auto rounded-lg bg-black/40">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="text-neutral-400">
                    <tr>
                        <th class="px-4 py-3">Фильм</th>
                        <th class="px-4 py-3">Зал</th>
                        <th class="px-4 py-3">Дата и время</th>
                        <th class="px-4 py-3">Заполняемость</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="session in stats.upcomingSessions"
                        :key="session.id"
                        class="border-t border-white/10"
                    >
                        <td class="px-4 py-3">{{ session.filmTitle }}</td>
                        <td class="px-4 py-3">{{ session.hallName }}</td>
                        <td class="px-4 py-3">
                            {{ formatFullDateTime(session.startsAt) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-1.5 w-24 overflow-hidden rounded-full bg-white/10"
                                >
                                    <div
                                        class="h-full rounded-full bg-red-600"
                                        :style="{ width: `${occupancyOf(session)}%` }"
                                    />
                                </div>
                                <span class="text-xs text-neutral-400"
                                    >{{ session.bookedSeats }} /
                                    {{ session.capacity }}</span
                                >
                            </div>
                        </td>
                    </tr>
                    <tr v-if="stats.upcomingSessions.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-500">
                            Ближайших сеансов пока нет.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
