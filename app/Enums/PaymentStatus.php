<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case CREATED  = 'created';
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case PAID     = 'paid';
    case REJECTED = 'rejected';
    case REFUNDED = 'refunded';
}
