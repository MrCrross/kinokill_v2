import { router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

/**
 * Drives an admin Index page's search/filter/pagination query params
 * against Inertia. Every admin list page (films/halls/sessions/users)
 * follows the same shape server-side (`search` + a few `?_id=` filters,
 * paginated with `?page=`), so this is the one place that builds the
 * query string.
 *
 * Bind `filters.<key>` directly with v-model — each key is watched and
 * re-submits on change (debounced for `search`, immediate otherwise), so
 * pages don't need to wire up their own input/change handlers.
 */
export function useAdminListFilters<F extends Record<string, string>>(
    url: string,
    initial: F,
) {
    const filters = reactive({ ...initial }) as F;

    let debounceTimer: ReturnType<typeof setTimeout> | undefined;

    function buildQuery(page?: number): Record<string, string> {
        const query: Record<string, string> = {};

        for (const [key, value] of Object.entries(filters)) {
            if (value) query[key] = value as string;
        }

        if (page && page > 1) {
            query.page = String(page);
        }

        return query;
    }

    function visit(page?: number) {
        router.get(url, buildQuery(page), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }

    for (const key of Object.keys(initial) as (keyof F)[]) {
        watch(
            () => filters[key],
            () => {
                if (key === 'search') {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => visit(), 300);
                } else {
                    visit();
                }
            },
        );
    }

    function goToPage(page: number) {
        visit(page);
    }

    return { filters, goToPage };
}
