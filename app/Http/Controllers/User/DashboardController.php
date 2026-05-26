<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user()->load([
            'rentals.item',
            'rentals.vendor',
            'rentals.returning',
        ]);

        $activeRentals = $user->rentals
            ->filter(fn ($r) => $r->isActive())
            ->sortBy(fn ($r) => $r->daysRemaining());

        $completedRentals = $user->rentals
            ->filter(fn ($r) => ! $r->isActive())
            ->sortByDesc('updated_at');

        return view('user.dashboard', compact('user', 'activeRentals', 'completedRentals'));
    }
}
