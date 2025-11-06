<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id;
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id;
    }
}
