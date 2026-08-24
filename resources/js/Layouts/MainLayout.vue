<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const permissions = computed(() => (page.props.auth?.permissions ?? []) as string[]);
const canManageAnything = computed(
    () =>
        permissions.value.includes('films.manage') ||
        permissions.value.includes('sessions.manage') ||
        permissions.value.includes('halls.manage'),
);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-neutral-950 text-neutral-100">
        <header class="border-b border-white/10 bg-black/40 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
                <Link href="/" class="text-lg font-semibold tracking-wide text-white">
                    KinoKill
                </Link>

                <nav class="flex items-center gap-6 text-sm">
                    <Link href="/films" class="text-neutral-300 hover:text-white">Фильмы</Link>
                    <Link href="/sessions" class="text-neutral-300 hover:text-white">Расписание</Link>

                    <Link
                        v-if="canManageAnything"
                        href="/admin"
                        class="text-amber-400 hover:text-amber-300"
                    >
                        Админка
                    </Link>

                    <template v-if="user">
                        <Link :href="route('profile.edit')" class="text-neutral-300 hover:text-white">
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
                        <Link :href="route('login')" class="text-neutral-300 hover:text-white">Вход</Link>
                        <Link
                            :href="route('register')"
                            class="rounded bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-500"
                        >
                            Регистрация
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            <div
                v-if="page.props.flash?.success"
                class="mb-6 rounded border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300"
            >
                {{ page.props.flash.success }}
            </div>
            <div
                v-if="page.props.flash?.error"
                class="mb-6 rounded border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300"
            >
                {{ page.props.flash.error }}
            </div>

            <slot />
        </main>
    </div>
</template>
