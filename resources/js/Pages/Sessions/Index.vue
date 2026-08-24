<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface SessionListItem {
    id: number;
    startsAt: string;
    hallName: string;
}

interface FilmListItem {
    id: number;
    title: string;
    country: string;
    ageLimit: number;
    posterUrl: string | null;
}

interface FilmSchedule {
    film: FilmListItem;
    sessions: SessionListItem[];
}

defineProps<{
    schedule: FilmSchedule[];
}>();

function formatTime(iso: string): string {
    return new Date(iso).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Расписание" />

    <MainLayout>
        <h1 class="mb-6 text-2xl font-semibold text-white">Расписание сеансов</h1>

        <p v-if="schedule.length === 0" class="text-neutral-400">
            Ближайших сеансов пока нет.
        </p>

        <div v-else class="space-y-6">
            <div
                v-for="entry in schedule"
                :key="entry.film.id"
                class="rounded-lg bg-black/40 p-5 backdrop-blur"
            >
                <div class="flex items-center gap-4">
                    <Link :href="`/films/${entry.film.id}`" class="font-medium text-white hover:underline">
                        {{ entry.film.title }}
                    </Link>
                    <span class="text-sm text-neutral-400">{{ entry.film.ageLimit }}+</span>
                </div>

                <div class="mt-3 flex flex-wrap gap-3">
                    <Link
                        v-for="session in entry.sessions"
                        :key="session.id"
                        :href="`/sessions/${session.id}`"
                        class="rounded border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10"
                    >
                        {{ formatTime(session.startsAt) }} · {{ session.hallName }}
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
