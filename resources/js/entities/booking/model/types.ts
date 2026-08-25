export interface BookingSeat {
    row: number;
    column: number;
    price: string;
}

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'expired';
export type PaymentMethod = 'cash' | 'card_stub';

export interface BookingDetail {
    id: number;
    status: BookingStatus;
    totalPrice: string;
    paymentMethod: PaymentMethod;
    filmTitle: string;
    hallName: string;
    startsAt: string;
    seats: BookingSeat[];
    canConfirm: boolean;
    canCancel: boolean;
}
