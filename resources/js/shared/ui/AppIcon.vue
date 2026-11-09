<script setup lang="ts">
import './icons';

import { Icon as IconifyIcon } from '@iconify/vue';

import type { AppIconName } from './icons';

withDefaults(defineProps<{ name: AppIconName; size?: number }>(), {
    size: 16,
});
</script>

<template>
    <IconifyIcon
        :icon="name"
        :width="size"
        :height="size"
        aria-hidden="true"
        class="shrink-0"
    />
</template>
