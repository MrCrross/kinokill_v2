<script setup lang="ts">
import type { Film, Genre } from '@/entities/film/model/types';
import AppIcon from '@/shared/ui/AppIcon.vue';
import InputError from '@/shared/ui/InputError.vue';
import InputLabel from '@/shared/ui/InputLabel.vue';
import PrimaryButton from '@/shared/ui/PrimaryButton.vue';
import Select, { type SelectOption } from '@/shared/ui/Select.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const NEW_PRODUCER = '__new__';

const props = withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        genres: Genre[];
        countries: string[];
        producers: string[];
        ageRatings: number[];
        film?: Film;
    }>(),
    {
        method: 'post',
        film: undefined,
    },
);

const form = useForm({
    title: props.film?.title ?? '',
    producer: props.film?.producer ?? '',
    country: props.film?.country ?? '',
    duration_minutes: String(props.film?.durationMinutes ?? 90),
    age_limit: String(props.film?.ageLimit ?? 0),
    rating: props.film?.rating ?? '',
    description: props.film?.description ?? '',
    genre_ids: props.film?.genres.map((genre) => genre.id) ?? ([] as number[]),
    poster: null as File | null,
});

const countryOptions = computed<SelectOption[]>(() =>
    props.countries.map((name) => ({ value: name, label: name })),
);

const ageOptions = computed<SelectOption[]>(() =>
    props.ageRatings.map((age) => ({ value: String(age), label: `${age}+` })),
);

const producerOptions = computed<SelectOption[]>(() => [
    ...props.producers.map((name) => ({ value: name, label: name })),
    { value: NEW_PRODUCER, label: 'Новый режиссёр…' },
]);

const isNewProducer = ref(
    props.film ? !props.producers.includes(props.film.producer) : false,
);
const producerSelection = ref(
    isNewProducer.value ? '' : (props.film?.producer ?? ''),
);

watch(producerSelection, (value) => {
    if (value === NEW_PRODUCER) {
        isNewProducer.value = true;
        form.producer = '';
        return;
    }
    form.producer = value;
});

function backToProducerList() {
    isNewProducer.value = false;
    producerSelection.value = '';
    form.producer = '';
}

const posterLabel = computed(
    () => form.poster?.name ?? 'Выберите файл изображения',
);

function onPosterChange(event: Event) {
    const target = event.target as HTMLInputElement;
    form.poster = target.files?.[0] ?? null;
}

function submit() {
    if (props.method === 'put') {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            props.action,
        );
        return;
    }
    form.post(props.action);
}
</script>

<template>
    <form
        class="max-w-2xl space-y-5 rounded-lg bg-black/40 p-6"
        @submit.prevent="submit"
    >
        <div v-if="film?.posterUrl" class="mb-2">
            <img
                :src="film.posterUrl"
                :alt="film.title"
                class="h-40 rounded object-cover"
            />
        </div>

        <div>
            <InputLabel value="Название" />
            <TextInput v-model="form.title" class="w-full" autofocus />
            <InputError :message="form.errors.title" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <InputLabel value="Режиссёр" />
                <template v-if="!isNewProducer">
                    <Select
                        v-model="producerSelection"
                        :options="producerOptions"
                        placeholder="Выберите режиссёра"
                    />
                </template>
                <div v-else class="flex items-start gap-2">
                    <TextInput
                        v-model="form.producer"
                        class="w-full"
                        placeholder="Имя и фамилия режиссёра"
                        autofocus
                    />
                    <button
                        type="button"
                        class="mt-2 shrink-0 text-xs text-neutral-400 underline hover:text-white"
                        @click="backToProducerList"
                    >
                        К списку
                    </button>
                </div>
                <InputError :message="form.errors.producer" />
            </div>
            <div>
                <InputLabel value="Страна" />
                <Select
                    v-model="form.country"
                    :options="countryOptions"
                    placeholder="Выберите страну"
                />
                <InputError :message="form.errors.country" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <InputLabel value="Длительность (мин)" />
                <TextInput
                    v-model="form.duration_minutes"
                    type="number"
                    min="1"
                    max="600"
                    class="w-full"
                />
                <InputError :message="form.errors.duration_minutes" />
            </div>
            <div>
                <InputLabel value="Возраст" />
                <Select
                    v-model="form.age_limit"
                    :options="ageOptions"
                    placeholder="Выберите возраст"
                />
                <InputError :message="form.errors.age_limit" />
            </div>
            <div>
                <InputLabel value="Рейтинг" />
                <TextInput
                    v-model="form.rating"
                    type="number"
                    min="0"
                    max="10"
                    step="0.1"
                    class="w-full"
                />
                <InputError :message="form.errors.rating" />
            </div>
        </div>

        <div>
            <InputLabel value="Описание" />
            <textarea
                v-model="form.description"
                rows="4"
                class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-neutral-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
            />
            <InputError :message="form.errors.description" />
        </div>

        <div>
            <InputLabel value="Жанры" />
            <div class="flex flex-wrap gap-x-4 gap-y-2">
                <label
                    v-for="genre in genres"
                    :key="genre.id"
                    class="flex cursor-pointer items-center gap-1.5 text-sm text-neutral-200"
                >
                    <input
                        v-model="form.genre_ids"
                        type="checkbox"
                        :value="genre.id"
                        class="rounded border-white/20 bg-white/5 text-red-500 focus:ring-red-500"
                    />
                    {{ genre.name }}
                </label>
            </div>
            <InputError :message="form.errors.genre_ids" />
        </div>

        <div>
            <InputLabel value="Постер" />
            <label
                class="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-white/15 bg-white/5 px-4 py-3 text-sm text-neutral-300 transition hover:border-red-500/50 hover:bg-white/10"
            >
                <AppIcon
                    name="image-plus"
                    :size="18"
                    class="text-neutral-400"
                />
                <span class="truncate">{{ posterLabel }}</span>
                <input
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    @change="onPosterChange"
                />
            </label>
            <InputError :message="form.errors.poster" />
        </div>

        <PrimaryButton type="submit" :disabled="form.processing">
            <AppIcon name="save" :size="16" />
            Сохранить
        </PrimaryButton>
    </form>
</template>
