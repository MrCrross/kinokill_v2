<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
import type { Film } from '@/entities/film/model/types';
import FilmPoster from '@/entities/film/ui/FilmPoster.vue';
import type { SessionListItem } from '@/entities/session/model/types';
import SessionTimeline from '@/widgets/session-timeline/ui/SessionTimeline.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    film: Film;
    sessions: SessionListItem[];
}>();
</script>

<template>
    <Head :title="film.title" />

    <MainLayout>
        <div class="flex flex-col gap-8 md:flex-row">
            <div
                class="w-full max-w-xs shrink-0 overflow-hidden rounded-lg bg-neutral-800"
            >
                <FilmPoster :poster-url="film.posterUrl" :title="film.title" />
            </div>

            <div class="flex-1 rounded-lg bg-black/40 p-6 backdrop-blur">
                <h1 class="text-2xl font-semibold text-white">
                    {{ film.title }}
                </h1>

                <dl
                    class="mt-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:grid-cols-3"
                >
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
                        <dd class="text-white">
                            {{ film.durationMinutes }} мин
                        </dd>
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
                        <dd class="text-white">
                            {{ film.genres.map((g) => g.name).join(', ') }}
                        </dd>
                    </div>
                </dl>

                <p class="mt-6 leading-relaxed text-neutral-200">
                    {{ film.description }}
                </p>

                <div class="mt-8">
                    <h2 class="mb-3 text-lg font-medium text-white">Сеансы</h2>
                    <SessionTimeline :sessions="sessions" />
                </div>
            </div>
        </div>
    </MainLayout>
</template>
