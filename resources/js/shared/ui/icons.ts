import { addIcon } from '@iconify/vue';

import armchair from '@iconify-icons/lucide/armchair';
import bookMarked from '@iconify-icons/lucide/book-marked';
import calendarDays from '@iconify-icons/lucide/calendar-days';
import check from '@iconify-icons/lucide/check';
import chevronDown from '@iconify-icons/lucide/chevron-down';
import clapperboard from '@iconify-icons/lucide/clapperboard';
import imagePlus from '@iconify-icons/lucide/image-plus';
import layoutDashboard from '@iconify-icons/lucide/layout-dashboard';
import plus from '@iconify-icons/lucide/plus';
import save from '@iconify-icons/lucide/save';
import send from '@iconify-icons/lucide/send';
import trash2 from '@iconify-icons/lucide/trash-2';
import users from '@iconify-icons/lucide/users';

const appIcons = {
    armchair,
    'book-marked': bookMarked,
    'calendar-days': calendarDays,
    check,
    'chevron-down': chevronDown,
    clapperboard,
    'image-plus': imagePlus,
    'layout-dashboard': layoutDashboard,
    plus,
    save,
    send,
    'trash-2': trash2,
    users,
} as const;

export type AppIconName = keyof typeof appIcons;

for (const [name, icon] of Object.entries(appIcons)) {
    addIcon(name, icon);
}
