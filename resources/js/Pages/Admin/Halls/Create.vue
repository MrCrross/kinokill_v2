<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    rows_count: 8,
    columns_count: 10,
    price_standard: 300,
    price_vip: 500,
});

function submit() {
    form.post('/admin/halls');
}
</script>

<template>
    <Head title="Добавить зал" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Добавить зал</h1>

        <form class="max-w-md space-y-4 rounded-lg bg-black/40 p-6" @submit.prevent="submit">
            <div>
                <label class="mb-1 block text-sm text-neutral-300">Название</label>
                <input v-model="form.name" type="text" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">{{ form.errors.name }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Рядов</label>
                    <input v-model.number="form.rows_count" type="number" min="1" max="40" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.rows_count" class="mt-1 text-xs text-red-400">{{ form.errors.rows_count }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Мест в ряду</label>
                    <input v-model.number="form.columns_count" type="number" min="1" max="40" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.columns_count" class="mt-1 text-xs text-red-400">{{ form.errors.columns_count }}</p>
                </div>
            </div>

            <p class="text-xs text-neutral-500">
                Последние два ряда автоматически становятся VIP-местами.
            </p>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Цена (стандарт)</label>
                    <input v-model.number="form.price_standard" type="number" min="0" step="0.01" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.price_standard" class="mt-1 text-xs text-red-400">{{ form.errors.price_standard }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300">Цена (VIP)</label>
                    <input v-model.number="form.price_vip" type="number" min="0" step="0.01" class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white" />
                    <p v-if="form.errors.price_vip" class="mt-1 text-xs text-red-400">{{ form.errors.price_vip }}</p>
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
