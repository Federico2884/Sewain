<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Returning;
use App\Services\DepositService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    public function __construct(private DepositService $depositService) {}

    private function vendor()
    {
        return Auth::guard('vendor')->user();
    }

    /**
     * User initiates return request (called via user-side route, but
     * stored here because return handling belongs to vendor).
     *
     * Alternatively expose this as a User controller action — see routes.
     */
    public function initiate(Request $request, Rental $rental)
    {
        abort_if($rental->user_id !== Auth::id(), 403);
        abort_if($rental->returning()->exists(), 422, 'Pengembalian sudah diajukan.');

        $data = $request->validate([
            'method' => ['required', Rule::in(['pickup', 'delivery'])],
        ]);

        Returning::create([
            'rental_id' => $rental->id,
            'method'    => $data['method'],
            'status'    => Returning::STATUS_PENDING,
            'verified'  => false,
        ]);

        return back()->with('success', 'Permintaan pengembalian berhasil dikirim ke vendor.');
    }

    /**
     * Vendor confirms condition and verifies the return.
     */
    public function verify(Request $request, Rental $rental)
    {
        $this->authorizeVendor($rental);

        $returning = $rental->returning;
        abort_if(! $returning || $returning->verified, 422);

        $data = $request->validate([
            'status' => ['required', Rule::in([Returning::STATUS_GOOD, Returning::STATUS_DAMAGED])],
        ]);

        $returning->update([
            'status'   => $data['status'],
            'verified' => true,
        ]);

        $refund = $this->depositService->calculateRefund($rental);

        return redirect()
            ->route('vendor.rentals.show', $rental)
            ->with('success', "Pengembalian dikonfirmasi. Deposit dikembalikan: Rp " . number_format($refund, 0, ',', '.'));
    }

    private function authorizeVendor(Rental $rental): void
    {
        abort_if($rental->vendor_id !== $this->vendor()->id, 403);
    }
}
