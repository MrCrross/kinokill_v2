<script setup lang="ts">
import GuestLayout from '@/app/layouts/GuestLayout.vue';
import { useI18n } from '@/shared/i18n';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const { t } = useI18n();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.forgot_password.title')" />

        <h1 class="mb-4 text-lg font-semibold text-white">
            {{ t('auth.forgot_password.title') }}
        </h1>

        <div class="mb-4 text-sm text-neutral-400">
            {{ t('auth.forgot_password.description') }}
        </div>

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

            <div class="mt-6 flex items-center justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ t('auth.forgot_password.submit') }}
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
