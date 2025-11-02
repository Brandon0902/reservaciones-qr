<?php

namespace App\Enums;

enum TicketStatus: string
{
    case UNUSED   = 'unused';
    case USED     = 'used';
    case EXPIRED  = 'expired';
    case CANCELED = 'canceled';
}
