<script setup lang="ts">
import UserMenu from '@/entities/user/ui/UserMenu.vue';
import { useI18n } from '@/shared/i18n';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const { locale } = useI18n();

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

const mobileMenuOpen = ref(false);
</script>

<template>
    <header class="border-b border-white/10 bg-black/40 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-4">
            <div class="flex items-center justify-between">
                <Link
                    href="/"
                    class="font-display text-xl font-semibold tracking-wider text-white uppercase"
                >
                    Kino<span class="text-red-500">Kill</span>
                </Link>

                <nav class="hidden items-center gap-6 text-sm sm:flex">
                    <Link
                        href="/films"
                        class="text-neutral-300 hover:text-white"
                        >Фильмы</Link
                    >
                    <Link
                        href="/sessions"
                        class="text-neutral-300 hover:text-white"
                        >Расписание</Link
                    >

                    <Link
                        v-if="canManageAnything"
                        href="/admin"
                        class="text-amber-400 hover:text-amber-300"
                    >
                        Админка
                    </Link>

                    <nav
                        class="flex items-center gap-1"
                        aria-label="Язык / Language"
                    >
                        <Link
                            :href="route('locale.update', 'ru')"
                            :class="
                                locale === 'ru'
                                    ? 'text-white'
                                    : 'text-neutral-500 hover:text-neutral-300'
                            "
                        >
                            RU
                        </Link>
                        <span class="text-neutral-700">/</span>
                        <Link
                            :href="route('locale.update', 'en')"
                            :class="
                                locale === 'en'
                                    ? 'text-white'
                                    : 'text-neutral-500 hover:text-neutral-300'
                            "
                        >
                            EN
                        </Link>
                    </nav>

                    <UserMenu :user="user" />
                </nav>

                <button
                    type="button"
                    class="rounded p-2 text-neutral-300 hover:bg-white/10 hover:text-white sm:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Открыть меню"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-6 w-6"
                    >
                        <path
                            v-if="!mobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <nav
                v-if="mobileMenuOpen"
                class="mt-4 flex flex-col gap-3 text-sm sm:hidden"
            >
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
                <nav class="flex items-center gap-1" aria-label="Язык / Language">
                    <Link
                        :href="route('locale.update', 'ru')"
                        :class="
                            locale === 'ru'
                                ? 'text-white'
                                : 'text-neutral-500 hover:text-neutral-300'
                        "
                    >
                        RU
                    </Link>
                    <span class="text-neutral-700">/</span>
                    <Link
                        :href="route('locale.update', 'en')"
                        :class="
                            locale === 'en'
                                ? 'text-white'
                                : 'text-neutral-500 hover:text-neutral-300'
                        "
                    >
                        EN
                    </Link>
                </nav>
                <UserMenu :user="user" />
            </nav>
        </div>
    </header>
</template>
