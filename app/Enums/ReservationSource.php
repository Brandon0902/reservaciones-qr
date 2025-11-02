<?php

namespace App\Enums;

enum ReservationSource: string
{
    case IN_PERSON = 'in_person';
    case PHONE     = 'phone';
    case WHATSAPP  = 'whatsapp';
    case WEB       = 'web';
    case OTHER     = 'other';
}
