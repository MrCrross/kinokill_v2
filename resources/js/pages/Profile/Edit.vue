<script setup lang="ts">
import AuthenticatedLayout from '@/app/layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Профиль" />

    <AuthenticatedLayout>
        <template #header>Профиль</template>

        <div class="max-w-xl space-y-6">
            <div
                class="rounded-lg border border-white/10 bg-black/40 p-6 backdrop-blur"
            >
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div
                class="rounded-lg border border-white/10 bg-black/40 p-6 backdrop-blur"
            >
                <UpdatePasswordForm />
            </div>

            <div
                class="rounded-lg border border-white/10 bg-black/40 p-6 backdrop-blur"
            >
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
