<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Genre {
    id: number;
    name: string;
}

interface Film {
    id: number;
    title: string;
    producer: string;
    country: string;
    durationMinutes: number;
    ageLimit: number;
    rating: string | null;
    description: string;
    posterUrl: string | null;
    genres: Genre[];
}

interface SessionListItem {
    id: number;
    startsAt: string;
    hallName: string;
}

defineProps<{
    film: Film;
    sessions: SessionListItem[];
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
    <Head :title="film.title" />

    <MainLayout>
        <div class="flex flex-col gap-8 md:flex-row">
            <div class="w-full max-w-xs shrink-0 overflow-hidden rounded-lg bg-neutral-800">
                <img
                    v-if="film.posterUrl"
                    :src="film.posterUrl"
                    :alt="film.title"
                    class="aspect-[2/3] w-full object-cover"
                />
                <div v-else class="flex aspect-[2/3] items-center justify-center text-neutral-600">
                    Нет постера
                </div>
            </div>

            <div class="flex-1 rounded-lg bg-black/40 p-6 backdrop-blur">
                <h1 class="text-2xl font-semibold text-white">{{ film.title }}</h1>

                <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-neutral-400">Режиссёр</dt>
                        <dd class="text-white">{{ film.producer }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-400">Страна</dt>
                        <dd class="text-white">{{ film.country }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-400">Длительность</dt>
                        <dd class="text-white">{{ film.durationMinutes }} мин</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-400">Возраст</dt>
                        <dd class="text-white">{{ film.ageLimit }}+</dd>
                    </div>
                    <div v-if="film.rating">
                        <dt class="text-neutral-400">Рейтинг</dt>
                        <dd class="text-white">{{ film.rating }}</dd>
                    </div>
                    <div v-if="film.genres.length">
                        <dt class="text-neutral-400">Жанры</dt>
                        <dd class="text-white">{{ film.genres.map((g) => g.name).join(', ') }}</dd>
                    </div>
                </dl>

                <p class="mt-6 leading-relaxed text-neutral-200">{{ film.description }}</p>

                <div class="mt-8">
                    <h2 class="mb-3 text-lg font-medium text-white">Сеансы</h2>
                    <p v-if="sessions.length === 0" class="text-neutral-400">
                        Сеансов пока не запланировано.
                    </p>
                    <div v-else class="flex flex-wrap gap-3">
                        <Link
                            v-for="session in sessions"
                            :key="session.id"
                            :href="`/sessions/${session.id}`"
                            class="rounded border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10"
                        >
                            {{ formatTime(session.startsAt) }} · {{ session.hallName }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
