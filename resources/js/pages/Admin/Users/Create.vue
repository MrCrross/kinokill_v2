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

interface RoleOption {
    id: number;
    name: string;
    label: string | null;
    description: string | null;
}

const props = defineProps<{
    roles: RoleOption[];
}>();

const roleOptions = computed(() =>
    props.roles.map((role) => ({
        value: String(role.id),
        label: role.label ?? role.name,
        description: role.description,
    })),
);

const form = useForm({
    name: '',
    email: '',
    role_id: '',
});

function submit() {
    form.post('/admin/users');
}
</script>

<template>
    <Head title="Добавить пользователя" />

    <AdminLayout>
        <h1 class="mb-4 text-xl font-semibold text-white">
            Добавить пользователя
        </h1>
        <p class="mb-4 max-w-xl text-sm text-neutral-500">
            Пароль не задаётся здесь — на указанный email будет отправлена
            ссылка для его установки.
        </p>

        <form
            class="max-w-md space-y-4 rounded-lg bg-black/40 p-6"
            @submit.prevent="submit"
        >
            <div>
                <InputLabel value="Имя" />
                <TextInput v-model="form.name" class="w-full" autofocus />
                <InputError :message="form.errors.name" />
            </div>

            <div>
                <InputLabel value="Email" />
                <TextInput v-model="form.email" type="email" class="w-full" />
                <InputError :message="form.errors.email" />
            </div>

            <div>
                <InputLabel value="Роль" />
                <Select
                    v-model="form.role_id"
                    :options="roleOptions"
                    placeholder="Без роли"
                />
                <InputError :message="form.errors.role_id" />
            </div>

            <PrimaryButton type="submit" :disabled="form.processing">
                <AppIcon name="send" :size="16" />
                Отправить приглашение
            </PrimaryButton>
        </form>
    </AdminLayout>
</template>
