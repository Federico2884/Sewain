<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Rental;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RentalController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    /**
     * Show booking form for a given item.
     */
    public function create(Request $request, Item $item)
    {
        if (! $item->availability) {
            return redirect()
                ->route('items.show', $item)
                ->with('error', 'Barang ini sedang tidak tersedia untuk disewa.');
        }

        $bookedDates = $item->bookedDates();
        $nextAvailableFrom = $item->nextAvailableFrom();
        $user = Auth::user();
        $deposit = round($item->deposit * $user->depositMultiplier(), 2);

        $today = now()->toDateString();

        $requestedStart = $request->query('start');
        $defaultStart = $today;

        if ($requestedStart && ! in_array($requestedStart, $bookedDates, true) && $requestedStart >= $today) {
            $defaultStart = $requestedStart;
        } elseif (in_array($today, $bookedDates, true) && $nextAvailableFrom) {
            $defaultStart = $nextAvailableFrom->toDateString();
        }

        return view('user.rentals.create', compact('item', 'bookedDates', 'deposit', 'defaultStart'));
    }

    /**
     * Validate booking data, stash in session, redirect to checkout page.
     * Rental record is NOT created yet — it's only created after payment succeeds.
     */
    public function store(Request $request, Item $item)
    {
        if (! $item->availability) {
            return redirect()
                ->route('items.show', $item)
                ->with('error', 'Barang ini sedang tidak tersedia untuk disewa.');
        }

        $data = $request->validate([
            'duration' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in(array_keys($item->offeredUnitPrices()))],
            'start' => ['required', 'date', 'after_or_equal:today'],
            'method' => ['required', Rule::in(['pickup', 'delivery'])],
        ], [
            'unit.in' => 'Durasi sewa ini tidak tersedia untuk barang tersebut.',
        ]);

        $this->ensureNoOverlap($item, $data['start'], (int) $data['duration'], $data['unit']);

        session()->put('booking', [
            'item_id' => $item->id,
            ...$data,
        ]);

        return redirect()->route('user.rentals.checkout', $item);
    }

    /**
     * Show payment method selection. Booking data comes from session.
     */
    public function checkout(Item $item)
    {
        $booking = session('booking');

        if (! $booking || $booking['item_id'] !== $item->id) {
            return redirect()
                ->route('user.rentals.create', $item)
                ->with('error', 'Sesi booking tidak ditemukan, silakan ulangi.');
        }

        if (! $item->availability) {
            session()->forget('booking');

            return redirect()
                ->route('items.show', $item)
                ->with('error', 'Barang sudah tidak tersedia.');
        }

        $user = Auth::user();
        $rentAmount = $item->priceFor($booking['unit']) * (int) $booking['duration'];
        $depositAmount = round((float) $item->deposit * $user->depositMultiplier(), 2);
        $total = $rentAmount + $depositAmount;

        return view('user.rentals.payment', compact('item', 'booking', 'rentAmount', 'depositAmount', 'total'));
    }

    /**
     * Demo payment: create rental + paid payment in one transaction.
     * Item availability flips to false here.
     */
    public function confirm(Request $request, Item $item)
    {
        $booking = session('booking');

        if (! $booking || $booking['item_id'] !== $item->id) {
            return redirect()
                ->route('user.rentals.create', $item)
                ->with('error', 'Sesi booking tidak ditemukan, silakan ulangi.');
        }

        if (! $item->availability) {
            session()->forget('booking');

            return redirect()
                ->route('items.show', $item)
                ->with('error', 'Barang sudah tidak tersedia.');
        }

        $data = $request->validate([
            'method' => ['required', Rule::in(array_keys(Payment::methods()))],
        ]);

        $this->ensureNoOverlap(
            $item,
            $booking['start'],
            (int) $booking['duration'],
            $booking['unit'],
        );

        $rental = DB::transaction(function () use ($item, $booking, $data) {
            $rental = Rental::create([
                'user_id' => Auth::id(),
                'vendor_id' => $item->vendor_id,
                'item_id' => $item->id,
                'duration' => $booking['duration'],
                'unit' => $booking['unit'],
                'start' => $booking['start'],
                'method' => $booking['method'],
            ]);

            $payment = $this->payments->createForRental($rental);
            $this->payments->processPayment($payment, $data['method']);

            $this->cancelOverlappingPending($rental);

            return $rental;
        });

        session()->forget('booking');

        return redirect()
            ->route('user.rentals.show', $rental)
            ->with('success', 'Pembayaran berhasil! Barang berhasil disewa.');
    }

    /**
     * Tandai semua pending rental untuk item & tanggal yang overlap dengan
     * rental yang baru saja paid sebagai failed (dibatalkan otomatis).
     */
    private function cancelOverlappingPending(Rental $paidRental): void
    {
        $paidStart = $paidRental->start;
        $paidEnd = $paidRental->expectedReturnDate();

        Rental::where('item_id', $paidRental->item_id)
            ->where('id', '!=', $paidRental->id)
            ->whereHas('payment', fn ($q) => $q->where('status', 'pending'))
            ->with('payment')
            ->get()
            ->filter(function (Rental $other) use ($paidStart, $paidEnd) {
                $otherEnd = $other->expectedReturnDate();

                return ! ($otherEnd->lt($paidStart) || $other->start->gt($paidEnd));
            })
            ->each(fn (Rental $r) => $this->payments->markFailed($r->payment));
    }

    /**
     * Show all rentals for the authenticated user.
     */
    public function index()
    {
        $rentals = Auth::user()
            ->rentals()
            ->with(['item', 'vendor', 'returning', 'payment'])
            ->latest()
            ->paginate(10);

        return view('user.rentals.index', compact('rentals'));
    }

    /**
     * Show rental detail.
     */
    public function show(Rental $rental)
    {
        $this->authorizeRental($rental);
        $rental->load('item', 'vendor', 'returning', 'reviews', 'payment');

        return view('user.rentals.show', compact('rental'));
    }

    /**
     * Lanjut bayar untuk rental yang masih pending — pindahkan datanya
     * ke session, hapus rental sementara, lalu redirect ke checkout.
     */
    public function continuePayment(Rental $rental)
    {
        $this->authorizeRental($rental);

        abort_if(! $rental->isPending(), 422, 'Rental ini tidak dalam status menunggu pembayaran.');

        if (! $rental->item->availability) {
            return redirect()
                ->route('user.rentals.index')
                ->with('error', 'Barang sudah tidak tersedia, rental ini dibatalkan.');
        }

        session()->put('booking', [
            'item_id' => $rental->item_id,
            'duration' => $rental->duration,
            'unit' => $rental->unit,
            'start' => $rental->start->toDateString(),
            'method' => $rental->method,
        ]);

        DB::transaction(function () use ($rental) {
            $rental->payment?->delete();
            $rental->delete();
        });

        return redirect()->route('user.rentals.checkout', $rental->item);
    }

    // ── Private ────────────────────────────────────────────────────────────────

    private function authorizeRental(Rental $rental): void
    {
        abort_if($rental->user_id !== Auth::id(), 403);
    }

    /**
     * Pastikan rentang sewa yang diminta tidak tabrakan dengan rental aktif lain.
     */
    private function ensureNoOverlap(Item $item, string $start, int $duration, string $unit): void
    {
        if ($item->overlapsBookedDates(Carbon::parse($start), $duration, $unit)) {
            throw ValidationException::withMessages([
                'start' => 'Tanggal yang dipilih bertabrakan dengan masa sewa lain. Silakan pilih tanggal lain.',
            ]);
        }
    }
}
