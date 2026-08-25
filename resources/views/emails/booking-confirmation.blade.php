<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Подтверждение брони</title>
</head>
<body style="margin:0; padding:24px; background:#f4f4f5; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e5e7eb;">
        <tr>
            <td style="background:#111827; padding:20px 24px;">
                <span style="color:#ffffff; font-size:18px; font-weight:bold; letter-spacing:0.05em; text-transform:uppercase;">KinoKill</span>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <h1 style="margin:0 0 4px; font-size:20px;">{{ $booking->cinemaSession->film->title }}</h1>
                <p style="margin:0 0 20px; color:#6b7280; font-size:14px;">
                    {{ $booking->cinemaSession->starts_at->translatedFormat('d.m.Y, H:i') }} &middot; {{ $booking->cinemaSession->hall->name }}
                </p>

                <table role="presentation" width="100%" style="border-collapse:collapse; margin-bottom:20px;">
                    <thead>
                        <tr>
                            <th align="left" style="font-size:12px; color:#6b7280; padding:6px 0; border-bottom:1px solid #e5e7eb;">Ряд</th>
                            <th align="left" style="font-size:12px; color:#6b7280; padding:6px 0; border-bottom:1px solid #e5e7eb;">Место</th>
                            <th align="right" style="font-size:12px; color:#6b7280; padding:6px 0; border-bottom:1px solid #e5e7eb;">Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking->bookingSeats as $bookingSeat)
                            <tr>
                                <td style="padding:6px 0; border-bottom:1px solid #f3f4f6;">{{ $bookingSeat->seat->row_number }}</td>
                                <td style="padding:6px 0; border-bottom:1px solid #f3f4f6;">{{ $bookingSeat->seat->column_number }}</td>
                                <td align="right" style="padding:6px 0; border-bottom:1px solid #f3f4f6;">{{ $bookingSeat->price }} ₽</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p style="margin:0 0 4px; font-size:14px; color:#6b7280;">Итого</p>
                <p style="margin:0 0 20px; font-size:24px; font-weight:bold;">{{ $booking->total_price }} ₽</p>

                <p style="margin:0; font-size:13px; color:#6b7280;">
                    Бронь №{{ $booking->id }} &middot;
                    {{ $booking->payment_method === \App\Enums\PaymentMethod::Cash ? 'оплата наличными в кассе' : 'оплата картой' }}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
