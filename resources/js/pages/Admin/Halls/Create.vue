<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import InputError from '@/shared/ui/InputError.vue';
import InputLabel from '@/shared/ui/InputLabel.vue';
import PrimaryButton from '@/shared/ui/PrimaryButton.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import HallSeatEditor, {
    type SeatState,
} from '@/widgets/hall-seat-editor/ui/HallSeatEditor.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

interface SeatMapEntry {
    row_number: number;
    column_number: number;
    seat_type: 'standard' | 'vip';
}

const form = useForm({
    name: '',
    rows_count: 8,
    columns_count: 10,
    price_standard: '300',
    price_vip: '500',
    seats: [] as SeatMapEntry[],
});

const cells = reactive<Record<string, SeatState>>({});

function defaultGrid(rows: number, columns: number): Record<string, SeatState> {
    const next: Record<string, SeatState> = {};

    for (let row = 1; row <= rows; row++) {
        const state: SeatState = row > rows - 2 ? 'vip' : 'standard';
        for (let column = 1; column <= columns; column++) {
            next[`${row}-${column}`] = state;
        }
    }

    return next;
}

function applyGrid(next: Record<string, SeatState>) {
    Object.keys(cells).forEach((key) => delete cells[key]);
    Object.assign(cells, next);
}

watch(
    () => [form.rows_count, form.columns_count],
    () => applyGrid(defaultGrid(form.rows_count, form.columns_count)),
    { immediate: true },
);

watch(
    cells,
    () => {
        form.seats = Object.entries(cells)
            .filter(
                (entry): entry is [string, 'standard' | 'vip'] =>
                    entry[1] !== null,
            )
            .map(([key, state]) => {
                const [row, column] = key.split('-').map(Number);
                return {
                    row_number: row,
                    column_number: column,
                    seat_type: state,
                };
            });
    },
    { deep: true, immediate: true },
);

function submit() {
    form.post('/admin/halls');
}
</script>

<template>
    <Head title="Добавить зал" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">Добавить зал</h1>

        <form
            class="max-w-3xl space-y-4 rounded-lg bg-black/40 p-6"
            @submit.prevent="submit"
        >
            <div>
                <InputLabel value="Название" />
                <TextInput v-model="form.name" class="w-full max-w-md" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid max-w-md grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Рядов" />
                    <input
                        v-model.number="form.rows_count"
                        type="number"
                        min="1"
                        max="40"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    />
                    <InputError :message="form.errors.rows_count" />
                </div>
                <div>
                    <InputLabel value="Мест в ряду" />
                    <input
                        v-model.number="form.columns_count"
                        type="number"
                        min="1"
                        max="40"
                        class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    />
                    <InputError :message="form.errors.columns_count" />
                </div>
            </div>

            <div class="grid max-w-md grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Цена (стандарт)" />
                    <TextInput
                        v-model="form.price_standard"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full"
                    />
                    <InputError :message="form.errors.price_standard" />
                </div>
                <div>
                    <InputLabel value="Цена (VIP)" />
                    <TextInput
                        v-model="form.price_vip"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full"
                    />
                    <InputError :message="form.errors.price_vip" />
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm text-neutral-300">Схема зала</label>
                    <button
                        type="button"
                        class="text-xs text-neutral-400 underline hover:text-white"
                        @click="
                            applyGrid(
                                defaultGrid(
                                    form.rows_count,
                                    form.columns_count,
                                ),
                            )
                        "
                    >
                        Сбросить к стандартной схеме
                    </button>
                </div>
                <p class="mb-3 text-xs text-neutral-500">
                    Клик по месту переключает его тип: стандарт → VIP → проход
                    (нет места). Схема фиксируется при создании зала и не может
                    быть изменена позже.
                </p>

                <HallSeatEditor
                    :rows="form.rows_count"
                    :columns="form.columns_count"
                    :model-value="cells"
                    @update:model-value="(value) => applyGrid(value)"
                />
                <p v-if="form.errors.seats" class="mt-2 text-xs text-red-400">
                    {{ form.errors.seats }}
                </p>
            </div>

            <PrimaryButton type="submit" :disabled="form.processing">
                Сохранить
            </PrimaryButton>
        </form>
    </AdminLayout>
</template>
