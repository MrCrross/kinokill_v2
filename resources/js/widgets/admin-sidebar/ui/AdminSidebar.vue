<script setup lang="ts">
import AppIcon from '@/shared/ui/AppIcon.vue';
import type { AppIconName } from '@/shared/ui/icons';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const permissions = computed(
    () => (page.props.auth?.permissions ?? []) as string[],
);

const sections = computed<
    {
        href: string;
        label: string;
        icon: AppIconName;
        show: boolean;
        exact: boolean;
    }[]
>(() => [
    {
        href: '/admin',
        label: 'Дашборд',
        icon: 'layout-dashboard',
        show: true,
        exact: true,
    },
    {
        href: '/admin/films',
        label: 'Фильмы',
        icon: 'clapperboard',
        show: permissions.value.includes('films.manage'),
        exact: false,
    },
    {
        href: '/admin/reference',
        label: 'Справочники',
        icon: 'book-marked',
        show: permissions.value.includes('films.manage'),
        exact: false,
    },
    {
        href: '/admin/halls',
        label: 'Залы',
        icon: 'armchair',
        show: permissions.value.includes('halls.manage'),
        exact: false,
    },
    {
        href: '/admin/sessions',
        label: 'Сеансы',
        icon: 'calendar-days',
        show: permissions.value.includes('sessions.manage'),
        exact: false,
    },
    {
        href: '/admin/users',
        label: 'Пользователи',
        icon: 'users',
        show: permissions.value.includes('users.manage-roles'),
        exact: false,
    },
]);

function isActive(section: { href: string; exact: boolean }): boolean {
    return section.exact
        ? page.url === section.href
        : page.url.startsWith(section.href);
}
</script>

<template>
    <nav class="flex gap-2 lg:flex-col">
        <Link
            v-for="section in sections.filter((s) => s.show)"
            :key="section.href"
            :href="section.href"
            class="flex items-center gap-2 rounded px-3 py-2 text-sm"
            :class="
                isActive(section)
                    ? 'bg-red-600/20 text-white'
                    : 'text-neutral-300 hover:bg-white/10 hover:text-white'
            "
        >
            <AppIcon :name="section.icon" :size="16" />
            {{ section.label }}
        </Link>
    </nav>
</template>
