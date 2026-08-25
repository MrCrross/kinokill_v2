<script setup lang="ts">
import GuestLayout from '@/app/layouts/GuestLayout.vue';
import { useI18n } from '@/shared/i18n';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const { t } = useI18n();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.reset_password.title')" />

        <h1 class="mb-6 text-lg font-semibold text-white">
            {{ t('auth.reset_password.title') }}
        </h1>

        <form @submit.prevent="submit">
            <div>
                <label for="email" class="mb-1 block text-sm text-neutral-300">
                    {{ t('auth.email') }}
                </label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    required
                    autofocus
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <div class="mt-4">
                <label
                    for="password"
                    class="mb-1 block text-sm text-neutral-300"
                >
                    {{ t('auth.password') }}
                </label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    required
                    autocomplete="new-password"
                />
                <p
                    v-if="form.errors.password"
                    class="mt-1 text-xs text-red-400"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="mt-4">
                <label
                    for="password_confirmation"
                    class="mb-1 block text-sm text-neutral-300"
                >
                    {{ t('auth.password_confirmation') }}
                </label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    required
                    autocomplete="new-password"
                />
                <p
                    v-if="form.errors.password_confirmation"
                    class="mt-1 text-xs text-red-400"
                >
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ t('auth.reset_password.submit') }}
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
