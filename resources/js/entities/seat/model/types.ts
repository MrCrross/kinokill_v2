export type SeatKind = 'standard' | 'vip';

export interface Seat {
    id: number;
    row: number;
    column: number;
    type: SeatKind;
    price: string;
    available: boolean;
}
