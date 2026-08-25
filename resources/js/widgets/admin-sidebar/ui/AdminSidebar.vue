<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const permissions = computed(
    () => (page.props.auth?.permissions ?? []) as string[],
);

const sections = computed(() => [
    { href: '/admin', label: 'Дашборд', show: true },
    {
        href: '/admin/films',
        label: 'Фильмы',
        show: permissions.value.includes('films.manage'),
    },
    {
        href: '/admin/halls',
        label: 'Залы',
        show: permissions.value.includes('halls.manage'),
    },
    {
        href: '/admin/sessions',
        label: 'Сеансы',
        show: permissions.value.includes('sessions.manage'),
    },
    {
        href: '/admin/users',
        label: 'Пользователи',
        show: permissions.value.includes('users.manage-roles'),
    },
]);
</script>

<template>
    <nav class="flex gap-2 lg:flex-col">
        <Link
            v-for="section in sections.filter((s) => s.show)"
            :key="section.href"
            :href="section.href"
            class="rounded px-3 py-2 text-sm text-neutral-300 hover:bg-white/10 hover:text-white"
        >
            {{ section.label }}
        </Link>
    </nav>
</template>
