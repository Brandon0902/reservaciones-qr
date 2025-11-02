<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Conteos por rol
        $totals = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total','role');

        $admins     = (int)($totals['admin'] ?? 0);
        $validators = (int)($totals['validator'] ?? 0);
        $customers  = (int)($totals['customer'] ?? 0);
        $totalUsers = $admins + $validators + $customers;

        return view('admin.dashboard', compact(
            'admins','validators','customers','totalUsers'
        ));
    }
}
