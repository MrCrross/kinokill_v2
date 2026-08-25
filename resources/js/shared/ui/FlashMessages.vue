<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
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
</template>
