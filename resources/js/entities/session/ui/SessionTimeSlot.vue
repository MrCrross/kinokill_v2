<script setup lang="ts">
import { formatSessionTime } from '@/shared/lib/formatDateTime';
import { Link } from '@inertiajs/vue3';

defineProps<{
    id: number;
    startsAt: string;
    hallName: string;
}>();
</script>

<template>
    <Link
        :href="`/sessions/${id}`"
        class="rounded border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10"
    >
        {{ formatSessionTime(startsAt) }} · {{ hallName }}
    </Link>
</template>
