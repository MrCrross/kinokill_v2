<script setup lang="ts">
import DangerButton from '@/shared/ui/DangerButton.vue';
import InputError from '@/shared/ui/InputError.vue';
import InputLabel from '@/shared/ui/InputLabel.vue';
import Modal from '@/shared/ui/Modal.vue';
import SecondaryButton from '@/shared/ui/SecondaryButton.vue';
import TextInput from '@/shared/ui/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-white">Удаление аккаунта</h2>

            <p class="mt-1 text-sm text-neutral-400">
                После удаления аккаунта все связанные с ним данные будут
                безвозвратно удалены. Перед удалением сохраните всё, что хотите
                оставить.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">
            Удалить аккаунт
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-white">
                    Вы уверены, что хотите удалить аккаунт?
                </h2>

                <p class="mt-1 text-sm text-neutral-400">
                    После удаления аккаунта все связанные с ним данные будут
                    безвозвратно удалены. Введите пароль, чтобы подтвердить
                    удаление.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Пароль" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Пароль"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">
                        Отмена
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Удалить аккаунт
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
