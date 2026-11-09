<script setup lang="ts">
import AdminLayout from '@/app/layouts/AdminLayout.vue';
import AppIcon from '@/shared/ui/AppIcon.vue';
import InputError from '@/shared/ui/InputError.vue';
import InputLabel from '@/shared/ui/InputLabel.vue';
import PrimaryButton from '@/shared/ui/PrimaryButton.vue';
import Select from '@/shared/ui/Select.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface FilmOption {
    id: number;
    title: string;
}

interface HallOption {
    id: number;
    name: string;
}

const props = defineProps<{
    films: FilmOption[];
    halls: HallOption[];
}>();

const filmOptions = computed(() =>
    props.films.map((film) => ({ value: String(film.id), label: film.title })),
);
const hallOptions = computed(() =>
    props.halls.map((hall) => ({ value: String(hall.id), label: hall.name })),
);

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

        <form
            class="max-w-md space-y-4 rounded-lg bg-black/40 p-6"
            @submit.prevent="submit"
        >
            <div>
                <InputLabel value="Фильм" />
                <Select
                    v-model="form.film_id"
                    :options="filmOptions"
                    placeholder="Выберите фильм"
                />
                <InputError :message="form.errors.film_id" />
            </div>

            <div>
                <InputLabel value="Зал" />
                <Select
                    v-model="form.hall_id"
                    :options="hallOptions"
                    placeholder="Выберите зал"
                />
                <InputError :message="form.errors.hall_id" />
            </div>

            <div>
                <InputLabel value="Дата и время" />
                <TextInput
                    v-model="form.starts_at"
                    type="datetime-local"
                    class="w-full"
                />
                <InputError :message="form.errors.starts_at" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Цена стандарт (необязательно)" />
                    <TextInput
                        v-model="form.price_standard"
                        type="number"
                        step="0.01"
                        placeholder="цена зала"
                        class="w-full"
                    />
                </div>
                <div>
                    <InputLabel value="Цена VIP (необязательно)" />
                    <TextInput
                        v-model="form.price_vip"
                        type="number"
                        step="0.01"
                        placeholder="цена зала"
                        class="w-full"
                    />
                </div>
            </div>

            <PrimaryButton type="submit" :disabled="form.processing">
                <AppIcon name="save" :size="16" />
                Сохранить
            </PrimaryButton>
        </form>
    </AdminLayout>
</template>
