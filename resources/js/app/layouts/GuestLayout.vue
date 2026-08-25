<script setup lang="ts">
import { useI18n } from '@/shared/i18n';
import { Link } from '@inertiajs/vue3';

const { locale } = useI18n();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-neutral-950 text-neutral-100">
        <header class="border-b border-white/10 bg-black/40 backdrop-blur">
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4"
            >
                <Link
                    href="/"
                    class="font-display text-xl font-semibold tracking-wider text-white uppercase"
                >
                    Kino<span class="text-red-500">Kill</span>
                </Link>

                <nav
                    class="flex items-center gap-2 text-sm"
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
            </div>
        </header>

        <main class="flex flex-1 items-center justify-center px-4 py-12">
            <div
                class="w-full max-w-md rounded-lg border border-white/10 bg-black/40 p-6 backdrop-blur"
            >
                <slot />
            </div>
        </main>
    </div>
</template>
