<script setup lang="ts">
import { onMounted, ref } from 'vue';

withDefaults(defineProps<{ type?: string }>(), { type: 'text' });

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        :type="type"
        class="rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-neutral-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none"
        v-model="model"
        ref="input"
    />
</template>
