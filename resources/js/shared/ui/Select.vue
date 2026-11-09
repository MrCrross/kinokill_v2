<script setup lang="ts">
import AppIcon from '@/shared/ui/AppIcon.vue';
import {
    SelectContent,
    SelectIcon,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectPortal,
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectViewport,
} from 'reka-ui';

export interface SelectOption {
    value: string;
    label: string;
    description?: string | null;
}

withDefaults(
    defineProps<{
        options: SelectOption[];
        placeholder?: string;
        disabled?: boolean;
    }>(),
    {
        placeholder: 'Выберите…',
        disabled: false,
    },
);

const model = defineModel<string>();

defineOptions({ inheritAttrs: false });
</script>

<template>
    <SelectRoot v-model="model" :disabled="disabled">
        <SelectTrigger
            v-bind="$attrs"
            class="flex w-full items-center justify-between gap-2 rounded border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 disabled:cursor-not-allowed disabled:opacity-50 data-[placeholder]:text-neutral-500"
        >
            <SelectValue :placeholder="placeholder" />
            <SelectIcon as="span" class="text-neutral-400">
                <AppIcon name="chevron-down" :size="16" />
            </SelectIcon>
        </SelectTrigger>

        <SelectPortal>
            <SelectContent
                class="z-50 max-h-72 overflow-hidden rounded-lg border border-white/10 bg-neutral-900 text-sm text-white shadow-xl"
                position="popper"
                :side-offset="4"
            >
                <SelectViewport class="p-1">
                    <SelectItem
                        v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                        :title="option.description ?? undefined"
                        class="flex cursor-pointer items-center justify-between gap-2 rounded px-3 py-2 outline-none select-none data-[highlighted]:bg-red-600 data-[highlighted]:text-white"
                    >
                        <SelectItemText>{{ option.label }}</SelectItemText>
                        <SelectItemIndicator class="text-red-400">
                            <AppIcon name="check" :size="16" />
                        </SelectItemIndicator>
                    </SelectItem>
                </SelectViewport>
            </SelectContent>
        </SelectPortal>
    </SelectRoot>
</template>
