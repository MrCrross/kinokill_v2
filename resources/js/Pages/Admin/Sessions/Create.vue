<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface FilmOption {
    id: number;
    title: string;
}

interface HallOption {
    id: number;
    name: string;
}

defineProps<{
    films: FilmOption[];
    halls: HallOption[];
}>();

const form = useForm({
    film_id: '',
    hall_id: '',
    starts_at: '',
    price_standard: '',
    price_vip: '',
});

function submit() {
    form.post('/admin/sessions');
}
</script>

<template>
    <Head title="Добавить сеанс" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Добавить сеанс</h1>

        <form class="max-w-md space-y-4 rounded-lg bg-black/40 p-6" @submit.prevent="submit">
            <div>
                <label class="mb-1 block text-sm text-neutral-300">Фильм</label>
                <select v-model="form.film_id" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white">
                    <option value="" disabled>Выберите фильм</option>
                    <option v-for="film in films" :key="film.id" :value="film.id">{{ film.title }}</option>
                </select>
                <p v-if="form.errors.film_id" class="mt-1 text-xs text-red-400">{{ form.errors.film_id }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300">Зал</label>
                <select v-model="form.hall_id" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white">
                    <option value="" disabled>Выберите зал</option>
                    <option v-for="hall in halls" :key="hall.id" :value="hall.id">{{ hall.name }}</option>
                </select>
                <p v-if="form.errors.hall_id" class="mt-1 text-xs text-red-400">{{ form.errors.hall_id }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm text-neutral-300">Дата и время</label>
                <input v-model="form.starts_at" type="datetime-local" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                <p v-if="form.errors.starts_at" class="mt-1 text-xs text-red-400">{{ form.errors.starts_at }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Цена стандарт (необязательно)</label>
                    <input v-model="form.price_standard" type="number" min="0" step="0.01" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" placeholder="цена зала" />
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Цена VIP (необязательно)</label>
                    <input v-model="form.price_vip" type="number" min="0" step="0.01" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" placeholder="цена зала" />
                </div>
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
