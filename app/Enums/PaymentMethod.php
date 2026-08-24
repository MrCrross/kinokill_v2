<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case CardStub = 'card_stub';
}
