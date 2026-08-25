<script setup lang="ts">
import FlashMessages from '@/shared/ui/FlashMessages.vue';
import SiteNavbar from '@/widgets/site-navbar/ui/SiteNavbar.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-neutral-950 text-neutral-100">
        <SiteNavbar />

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            <FlashMessages />

            <slot />
        </main>
    </div>
</template>
