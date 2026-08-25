<script setup lang="ts">
import GuestLayout from '@/app/layouts/GuestLayout.vue';
import { useI18n } from '@/shared/i18n';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.login.title')" />

        <h1 class="mb-6 text-lg font-semibold text-white">
            {{ t('auth.login.title') }}
        </h1>

        <div v-if="status" class="mb-4 text-sm font-medium text-emerald-400">
            {{ status }}
        </div>

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
                    autocomplete="current-password"
                />
                <p
                    v-if="form.errors.password"
                    class="mt-1 text-xs text-red-400"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="mt-4 flex items-center">
                <label class="flex items-center gap-2 text-sm text-neutral-300">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        name="remember"
                        class="rounded border-white/20 bg-white/5 text-red-500 focus:ring-red-500"
                    />
                    {{ t('auth.login.remember') }}
                </label>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-neutral-400 underline hover:text-white"
                >
                    {{ t('auth.login.forgot_password') }}
                </Link>
                <span v-else />

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ t('auth.login.submit') }}
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
