export interface Genre {
    id: number;
    name: string;
}

export interface FilmListItem {
    id: number;
    title: string;
    country: string;
    ageLimit: number;
    posterUrl: string | null;
}

export interface Film {
    id: number;
    title: string;
    producer: string;
    country: string;
    durationMinutes: number;
    ageLimit: number;
    rating: string | null;
    description: string;
    posterUrl: string | null;
    genres: Genre[];
}
