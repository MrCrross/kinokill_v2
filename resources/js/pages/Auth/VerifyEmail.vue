<script setup lang="ts">
import GuestLayout from '@/app/layouts/GuestLayout.vue';
import { useI18n } from '@/shared/i18n';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    status?: string;
}>();

const { t } = useI18n();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.verify_email.title')" />

        <h1 class="mb-4 text-lg font-semibold text-white">
            {{ t('auth.verify_email.title') }}
        </h1>

        <div class="mb-4 text-sm text-neutral-400">
            {{ t('auth.verify_email.description') }}
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 text-sm font-medium text-emerald-400"
        >
            {{ t('auth.verify_email.link_sent') }}
        </div>

        <form @submit.prevent="submit">
            <div class="flex items-center justify-between">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ t('auth.verify_email.submit') }}
                </button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-neutral-400 underline hover:text-white"
                >
                    {{ t('auth.verify_email.logout') }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
