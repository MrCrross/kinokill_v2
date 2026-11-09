export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type Locale = 'ru' | 'en';

export interface Paginated<T> {
    data: T[];
    currentPage: number;
    lastPage: number;
    perPage: number;
    total: number;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
        roles: string[];
        permissions: string[];
    };
    flash: {
        success?: string | null;
        error?: string | null;
    };
    locale: Locale;
};
