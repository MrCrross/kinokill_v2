import type { PageProps } from '@/shared/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type MessageKey, messages } from './messages';

export function useI18n() {
    const page = usePage<PageProps>();
    const locale = computed(() => page.props.locale ?? 'ru');

    function t(key: MessageKey): string {
        return messages[locale.value][key] ?? messages.ru[key] ?? key;
    }

    return { locale, t };
}
