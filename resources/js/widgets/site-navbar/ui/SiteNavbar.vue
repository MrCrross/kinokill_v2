<script setup lang="ts">
import UserMenu from '@/entities/user/ui/UserMenu.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const permissions = computed(
    () => (page.props.auth?.permissions ?? []) as string[],
);
const canManageAnything = computed(
    () =>
        permissions.value.includes('films.manage') ||
        permissions.value.includes('sessions.manage') ||
        permissions.value.includes('halls.manage'),
);
</script>

<template>
    <header class="border-b border-white/10 bg-black/40 backdrop-blur">
        <div
            class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4"
        >
            <Link
                href="/"
                class="text-lg font-semibold tracking-wide text-white"
            >
                KinoKill
            </Link>

            <nav class="flex items-center gap-6 text-sm">
                <Link href="/films" class="text-neutral-300 hover:text-white"
                    >Фильмы</Link
                >
                <Link href="/sessions" class="text-neutral-300 hover:text-white"
                    >Расписание</Link
                >

                <Link
                    v-if="canManageAnything"
                    href="/admin"
                    class="text-amber-400 hover:text-amber-300"
                >
                    Админка
                </Link>

                <UserMenu :user="user" />
            </nav>
        </div>
    </header>
</template>
