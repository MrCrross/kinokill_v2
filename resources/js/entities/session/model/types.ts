export interface Hall {
    id: number;
    name: string;
    rowsCount: number;
    columnsCount: number;
}

export interface SessionListItem {
    id: number;
    startsAt: string;
    hallName: string;
}

export interface SessionDetail {
    id: number;
    filmTitle: string;
    startsAt: string;
    hall: Hall;
    seats: import('@/entities/seat/model/types').Seat[];
}

export interface FilmSchedule {
    film: import('@/entities/film/model/types').FilmListItem;
    sessions: SessionListItem[];
}
