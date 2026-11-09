<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import AppIcon from '@/shared/ui/AppIcon.vue';
import InputError from '@/shared/ui/InputError.vue';
import InputLabel from '@/shared/ui/InputLabel.vue';
import PrimaryButton from '@/shared/ui/PrimaryButton.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import HallSeatEditor, {
    type SeatState,
} from '@/widgets/hall-seat-editor/ui/HallSeatEditor.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface SeatMapEntry {
    rowNumber: number;
    columnNumber: number;
    seatType: 'standard' | 'vip';
}

interface Hall {
    id: number;
    name: string;
    rowsCount: number;
    columnsCount: number;
    priceStandard: string;
    priceVip: string;
    seats: SeatMapEntry[];
}

const props = defineProps<{
    hall: Hall;
}>();

const cells = computed<Record<string, SeatState>>(() => {
    const map: Record<string, SeatState> = {};
    props.hall.seats.forEach((seat) => {
        map[`${seat.rowNumber}-${seat.columnNumber}`] = seat.seatType;
    });
    return map;
});

const form = useForm({
    name: props.hall.name,
    price_standard: props.hall.priceStandard,
    price_vip: props.hall.priceVip,
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

        <div class="mb-6 max-w-3xl">
            <HallSeatEditor
                :rows="hall.rowsCount"
                :columns="hall.columnsCount"
                :model-value="cells"
                readonly
            />
        </div>

        <form
            class="max-w-md space-y-4 rounded-lg bg-black/40 p-6"
            @submit.prevent="submit"
        >
            <div>
                <InputLabel value="Название" />
                <TextInput v-model="form.name" class="w-full" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid grid-cols-2 gap-4">
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

            <PrimaryButton type="submit" :disabled="form.processing">
                <AppIcon name="save" :size="16" />
                Сохранить
            </PrimaryButton>
        </form>
    </AdminLayout>
</template>
