<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import AppIcon from '@/shared/ui/AppIcon.vue';
import InputError from '@/shared/ui/InputError.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

interface Genre {
    id: number;
    name: string;
    filmsCount: number;
}

defineProps<{
    countries: string[];
    directors: string[];
    genres: Genre[];
}>();

const countryForm = useForm({ name: '' });
const directorForm = useForm({ name: '' });
const genreForm = useForm({ name: '' });

function addCountry() {
    countryForm.post('/admin/reference/countries', {
        preserveScroll: true,
        onSuccess: () => countryForm.reset(),
    });
}

function addDirector() {
    directorForm.post('/admin/reference/directors', {
        preserveScroll: true,
        onSuccess: () => directorForm.reset(),
    });
}

function addGenre() {
    genreForm.post('/admin/reference/genres', {
        preserveScroll: true,
        onSuccess: () => genreForm.reset(),
    });
}

function destroyCountry(name: string) {
    if (confirm(`Удалить страну «${name}»?`)) {
        router.delete(`/admin/reference/countries/${encodeURIComponent(name)}`);
    }
}

function destroyDirector(name: string) {
    if (confirm(`Удалить режиссёра «${name}»?`)) {
        router.delete(`/admin/reference/directors/${encodeURIComponent(name)}`);
    }
}

function destroyGenre(genre: Genre) {
    if (confirm(`Удалить жанр «${genre.name}»?`)) {
        router.delete(`/admin/reference/genres/${genre.id}`);
    }
}
</script>

<template>
    <Head title="Справочники" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Справочники</h1>
        <p class="mb-6 max-w-2xl text-sm text-neutral-500">
            Списки стран, режиссёров и жанров, из которых заполняется форма
            фильма. Новый режиссёр, введённый прямо в форме фильма, также
            попадает сюда автоматически.
        </p>

        <div class="grid max-w-4xl gap-6 md:grid-cols-2 xl:grid-cols-3">
            <section class="rounded-lg bg-black/40 p-5">
                <h2 class="mb-4 text-sm font-semibold text-neutral-200">
                    Страны
                </h2>

                <form
                    class="mb-4 flex items-start gap-2"
                    @submit.prevent="addCountry"
                >
                    <div class="flex-1">
                        <TextInput
                            v-model="countryForm.name"
                            class="w-full"
                            placeholder="Новая страна"
                        />
                        <InputError :message="countryForm.errors.name" />
                    </div>
                    <button
                        type="submit"
                        class="mt-1 shrink-0 rounded bg-red-600 p-2 text-white transition hover:bg-red-500 disabled:opacity-50"
                        :disabled="countryForm.processing"
                        title="Добавить страну"
                    >
                        <AppIcon name="plus" :size="16" />
                    </button>
                </form>

                <ul class="space-y-1">
                    <li
                        v-for="name in countries"
                        :key="name"
                        class="flex items-center justify-between rounded px-2 py-1.5 text-sm text-neutral-200 hover:bg-white/5"
                    >
                        {{ name }}
                        <button
                            type="button"
                            class="text-neutral-500 transition hover:text-red-400"
                            title="Удалить"
                            @click="destroyCountry(name)"
                        >
                            <AppIcon name="trash-2" :size="14" />
                        </button>
                    </li>
                    <li
                        v-if="countries.length === 0"
                        class="text-sm text-neutral-500"
                    >
                        Пока пусто.
                    </li>
                </ul>
            </section>

            <section class="rounded-lg bg-black/40 p-5">
                <h2 class="mb-4 text-sm font-semibold text-neutral-200">
                    Режиссёры
                </h2>

                <form
                    class="mb-4 flex items-start gap-2"
                    @submit.prevent="addDirector"
                >
                    <div class="flex-1">
                        <TextInput
                            v-model="directorForm.name"
                            class="w-full"
                            placeholder="Новый режиссёр"
                        />
                        <InputError :message="directorForm.errors.name" />
                    </div>
                    <button
                        type="submit"
                        class="mt-1 shrink-0 rounded bg-red-600 p-2 text-white transition hover:bg-red-500 disabled:opacity-50"
                        :disabled="directorForm.processing"
                        title="Добавить режиссёра"
                    >
                        <AppIcon name="plus" :size="16" />
                    </button>
                </form>

                <ul class="space-y-1">
                    <li
                        v-for="name in directors"
                        :key="name"
                        class="flex items-center justify-between rounded px-2 py-1.5 text-sm text-neutral-200 hover:bg-white/5"
                    >
                        {{ name }}
                        <button
                            type="button"
                            class="text-neutral-500 transition hover:text-red-400"
                            title="Удалить"
                            @click="destroyDirector(name)"
                        >
                            <AppIcon name="trash-2" :size="14" />
                        </button>
                    </li>
                    <li
                        v-if="directors.length === 0"
                        class="text-sm text-neutral-500"
                    >
                        Пока пусто.
                    </li>
                </ul>
            </section>

            <section class="rounded-lg bg-black/40 p-5">
                <h2 class="mb-4 text-sm font-semibold text-neutral-200">
                    Жанры
                </h2>

                <form
                    class="mb-4 flex items-start gap-2"
                    @submit.prevent="addGenre"
                >
                    <div class="flex-1">
                        <TextInput
                            v-model="genreForm.name"
                            class="w-full"
                            placeholder="Новый жанр"
                        />
                        <InputError :message="genreForm.errors.name" />
                    </div>
                    <button
                        type="submit"
                        class="mt-1 shrink-0 rounded bg-red-600 p-2 text-white transition hover:bg-red-500 disabled:opacity-50"
                        :disabled="genreForm.processing"
                        title="Добавить жанр"
                    >
                        <AppIcon name="plus" :size="16" />
                    </button>
                </form>

                <ul class="space-y-1">
                    <li
                        v-for="genre in genres"
                        :key="genre.id"
                        class="flex items-center justify-between rounded px-2 py-1.5 text-sm text-neutral-200 hover:bg-white/5"
                    >
                        <span>
                            {{ genre.name }}
                            <span class="ml-1 text-xs text-neutral-500">
                                {{ genre.filmsCount }}
                            </span>
                        </span>
                        <button
                            type="button"
                            class="transition disabled:cursor-not-allowed disabled:opacity-30"
                            :class="
                                genre.filmsCount > 0
                                    ? 'text-neutral-600'
                                    : 'text-neutral-500 hover:text-red-400'
                            "
                            :disabled="genre.filmsCount > 0"
                            :title="
                                genre.filmsCount > 0
                                    ? 'Жанр привязан к фильмам'
                                    : 'Удалить'
                            "
                            @click="destroyGenre(genre)"
                        >
                            <AppIcon name="trash-2" :size="14" />
                        </button>
                    </li>
                    <li
                        v-if="genres.length === 0"
                        class="text-sm text-neutral-500"
                    >
                        Пока пусто.
                    </li>
                </ul>
            </section>
        </div>
    </AdminLayout>
</template>
