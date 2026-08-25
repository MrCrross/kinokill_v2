<script setup lang="ts">
import type { User } from '@/entities/user/model/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    user: User | null;
}>();
</script>

<template>
    <template v-if="user">
        <Link
            :href="route('profile.edit')"
            class="text-neutral-300 hover:text-white"
        >
            {{ user.name }}
        </Link>
        <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="text-neutral-400 hover:text-white"
        >
            Выйти
        </Link>
    </template>
    <template v-else>
        <Link :href="route('login')" class="text-neutral-300 hover:text-white"
            >Вход</Link
        >
        <Link
            :href="route('register')"
            class="rounded bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-500"
        >
            Регистрация
        </Link>
    </template>
</template>
