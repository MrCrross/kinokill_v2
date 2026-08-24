<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Genre {
    id: number;
    name: string;
}

defineProps<{
    genres: Genre[];
}>();

const form = useForm({
    title: '',
    producer: '',
    country: '',
    duration_minutes: 90,
    age_limit: 0,
    rating: '',
    description: '',
    genre_ids: [] as number[],
    poster: null as File | null,
});

function onPosterChange(event: Event) {
    const target = event.target as HTMLInputElement;
    form.poster = target.files?.[0] ?? null;
}

function submit() {
    form.post('/admin/films');
}
</script>

<template>
    <Head title="Добавить фильм" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Добавить фильм</h1>

        <form class="max-w-xl space-y-4 rounded-lg bg-black/40 p-6" @submit.prevent="submit">
            <div>
                <label class="mb-1 block text-sm text-neutral-300">Название</label>
                <input v-model="form.title" type="text" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                <p v-if="form.errors.title" class="mt-1 text-xs text-red-400">{{ form.errors.title }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Режиссёр</label>
                    <input v-model="form.producer" type="text" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.producer" class="mt-1 text-xs text-red-400">{{ form.errors.producer }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Страна</label>
                    <input v-model="form.country" type="text" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.country" class="mt-1 text-xs text-red-400">{{ form.errors.country }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Длительность (мин)</label>
                    <input v-model.number="form.duration_minutes" type="number" min="1" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.duration_minutes" class="mt-1 text-xs text-red-400">{{ form.errors.duration_minutes }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Возраст</label>
                    <input v-model.number="form.age_limit" type="number" min="0" max="21" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.age_limit" class="mt-1 text-xs text-red-400">{{ form.errors.age_limit }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Рейтинг</label>
                    <input v-model="form.rating" type="number" step="0.1" min="0" max="10" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.rating" class="mt-1 text-xs text-red-400">{{ form.errors.rating }}</p>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300">Описание</label>
                <textarea v-model="form.description" rows="4" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                <p v-if="form.errors.description" class="mt-1 text-xs text-red-400">{{ form.errors.description }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300">Жанры</label>
                <div class="flex flex-wrap gap-3">
                    <label v-for="genre in genres" :key="genre.id" class="flex items-center gap-1 text-sm text-neutral-200">
                        <input v-model="form.genre_ids" type="checkbox" :value="genre.id" />
                        {{ genre.name }}
                    </label>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300">Постер</label>
                <input type="file" accept="image/*" class="w-full text-sm text-neutral-300" @change="onPosterChange" />
                <p v-if="form.errors.poster" class="mt-1 text-xs text-red-400">{{ form.errors.poster }}</p>
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
