<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import type { Film, Genre } from '@/entities/film/model/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    film: Film;
    genres: Genre[];
}>();

const form = useForm({
    title: props.film.title,
    producer: props.film.producer,
    country: props.film.country,
    duration_minutes: props.film.durationMinutes,
    age_limit: props.film.ageLimit,
    rating: props.film.rating ?? '',
    description: props.film.description,
    genre_ids: props.film.genres.map((g) => g.id),
    poster: null as File | null,
});

function onPosterChange(event: Event) {
    const target = event.target as HTMLInputElement;
    form.poster = target.files?.[0] ?? null;
}

function submit() {
    form.transform((data) => ({ ...data, _method: 'put' })).post(
        `/admin/films/${props.film.id}`,
    );
}
</script>

<template>
    <Head :title="`Редактировать «${film.title}»`" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">
            Редактировать «{{ film.title }}»
        </h1>

        <form
            class="max-w-xl space-y-4 rounded-lg bg-black/40 p-6"
            @submit.prevent="submit"
        >
            <div v-if="film.posterUrl">
                <img
                    :src="film.posterUrl"
                    :alt="film.title"
                    class="h-40 rounded object-cover"
                />
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300"
                    >Название</label
                >
                <input
                    v-model="form.title"
                    type="text"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                />
                <p v-if="form.errors.title" class="mt-1 text-xs text-red-400">
                    {{ form.errors.title }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Режиссёр</label
                    >
                    <input
                        v-model="form.producer"
                        type="text"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Страна</label
                    >
                    <input
                        v-model="form.country"
                        type="text"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Длительность (мин)</label
                    >
                    <input
                        v-model.number="form.duration_minutes"
                        type="number"
                        min="1"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Возраст</label
                    >
                    <input
                        v-model.number="form.age_limit"
                        type="number"
                        min="0"
                        max="21"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Рейтинг</label
                    >
                    <input
                        v-model="form.rating"
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300"
                    >Описание</label
                >
                <textarea
                    v-model="form.description"
                    rows="4"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                />
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300">Жанры</label>
                <div class="flex flex-wrap gap-3">
                    <label
                        v-for="genre in genres"
                        :key="genre.id"
                        class="flex items-center gap-1 text-sm text-neutral-200"
                    >
                        <input
                            v-model="form.genre_ids"
                            type="checkbox"
                            :value="genre.id"
                        />
                        {{ genre.name }}
                    </label>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300"
                    >Заменить постер</label
                >
                <input
                    type="file"
                    accept="image/*"
                    class="w-full text-sm text-neutral-300"
                    @change="onPosterChange"
                />
                <p v-if="form.errors.poster" class="mt-1 text-xs text-red-400">
                    {{ form.errors.poster }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-500 disabled:opacity-50"
            >
                Сохранить
            </button>
        </form>
    </AdminLayout>
</template>
