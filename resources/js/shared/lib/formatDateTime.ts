export function formatSessionTime(iso: string): string {
    return new Date(iso).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function formatFullDateTime(iso: string): string {
    return new Date(iso).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

/**
 * Label for a schedule day tab. `index` is the day's position in the
 * always-today-first 7-day list, so 0/1 can be special-cased as
 * "Сегодня"/"Завтра" without comparing dates client-side.
 */
export function formatScheduleDayLabel(date: string, index: number): string {
    if (index === 0) return 'Сегодня';
    if (index === 1) return 'Завтра';

    const label = new Date(`${date}T00:00:00`).toLocaleDateString('ru-RU', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
    });

    return label.charAt(0).toUpperCase() + label.slice(1);
}
