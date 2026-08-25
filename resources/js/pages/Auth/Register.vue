<script setup lang="ts">
import GuestLayout from '@/app/layouts/GuestLayout.vue';
import { useI18n } from '@/shared/i18n';
import { Head, Link, useForm } from '@inertiajs/vue3';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.register.title')" />

        <h1 class="mb-6 text-lg font-semibold text-white">
            {{ t('auth.register.title') }}
        </h1>

        <form @submit.prevent="submit">
            <div>
                <label for="name" class="mb-1 block text-sm text-neutral-300">
                    {{ t('auth.name') }}
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    required
                    autofocus
                    autocomplete="name"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="mt-4">
                <label for="email" class="mb-1 block text-sm text-neutral-300">
                    {{ t('auth.email') }}
                </label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="w-full rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    required
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

            <div class="mt-6 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="text-sm text-neutral-400 underline hover:text-white"
                >
                    {{ t('auth.register.already_registered') }}
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ t('auth.register.submit') }}
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
