<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case DEPOSIT  = 'deposit';
    case TRANSFER = 'transfer';
}
