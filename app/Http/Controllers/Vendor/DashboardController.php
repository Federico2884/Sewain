<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->load([
            'items',
            'rentals.item',
            'rentals.user',
            'rentals.returning',
        ]);

        $activeRentals = $vendor->rentals
            ->filter(fn ($r) => $r->isActive())
            ->sortBy(fn ($r) => $r->daysRemaining());

        $pendingReturns = $vendor->rentals
            ->filter(fn ($r) => $r->returning && ! $r->returning->verified);

        $completedRentals = $vendor->rentals
            ->filter(fn ($r) => ! $r->isActive())
            ->sortByDesc('updated_at');

        return view('vendor.dashboard', compact(
            'vendor',
            'activeRentals',
            'pendingReturns',
            'completedRentals',
        ));
    }
}
