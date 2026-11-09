<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
import type { FilmSchedule, ScheduleDay } from '@/entities/session/model/types';
import ScheduleDayTabs from '@/widgets/schedule-day-tabs/ui/ScheduleDayTabs.vue';
import SessionTimeline from '@/widgets/session-timeline/ui/SessionTimeline.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    schedule: FilmSchedule[];
    days: ScheduleDay[];
}>();
</script>

<template>
    <Head title="Расписание" />

    <MainLayout>
        <h1
            class="font-display mb-6 text-3xl font-semibold tracking-wide text-white uppercase"
        >
            Расписание сеансов
        </h1>

        <ScheduleDayTabs :days="days" />

        <p v-if="schedule.length === 0" class="text-neutral-400">
            На эту дату сеансов нет.
        </p>

        <div v-else class="space-y-6">
            <div
                v-for="entry in schedule"
                :key="entry.film.id"
                class="rounded-lg bg-black/40 p-5 backdrop-blur"
            >
                <div class="flex items-center gap-4">
                    <Link
                        :href="`/films/${entry.film.id}`"
                        class="font-medium text-white hover:underline"
                    >
                        {{ entry.film.title }}
                    </Link>
                    <span class="text-sm text-neutral-400"
                        >{{ entry.film.ageLimit }}+</span
                    >
                </div>

                <div class="mt-3">
                    <SessionTimeline :sessions="entry.sessions" />
                </div>
            </div>
        </div>
    </MainLayout>
</template>
