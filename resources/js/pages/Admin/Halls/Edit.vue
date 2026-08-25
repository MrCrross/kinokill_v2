<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Hall {
    id: number;
    name: string;
    rowsCount: number;
    columnsCount: number;
    priceStandard: string;
    priceVip: string;
}

const props = defineProps<{
    hall: Hall;
}>();

const form = useForm({
    name: props.hall.name,
    price_standard: Number(props.hall.priceStandard),
    price_vip: Number(props.hall.priceVip),
});

function submit() {
    form.put(`/admin/halls/${props.hall.id}`);
}
</script>

<template>
    <Head :title="`Редактировать «${hall.name}»`" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">
            Редактировать «{{ hall.name }}»
        </h1>
        <p class="mb-4 text-sm text-neutral-500">
            Схема зала ({{ hall.rowsCount }} × {{ hall.columnsCount }})
            фиксируется при создании и не может быть изменена, чтобы не сломать
            уже проданные билеты.
        </p>

        <form
            class="max-w-md space-y-4 rounded-lg bg-black/40 p-6"
            @submit.prevent="submit"
        >
            <div>
                <label class="mb-1 block text-sm text-neutral-300"
                    >Название</label
                >
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Цена (стандарт)</label
                    >
                    <input
                        v-model.number="form.price_standard"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm text-neutral-300"
                        >Цена (VIP)</label
                    >
                    <input
                        v-model.number="form.price_vip"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-white"
                    />
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
