<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Buat payment record untuk rental yang baru dibuat.
     * Idempotent — jika sudah ada record, kembalikan yang ada.
     */
    public function createForRental(Rental $rental): Payment
    {
        if ($rental->payment) {
            return $rental->payment;
        }

        return Payment::create([
            'rental_id'      => $rental->id,
            'amount'         => $rental->grandTotal(),
            'rent_amount'    => $rental->totalCost(),
            'deposit_amount' => $rental->adjustedDeposit(),
            'status'         => Payment::STATUS_PENDING,
        ]);
    }

    /**
     * Demo mode: proses pembayaran secara instan (simulasi e-wallet/transfer).
     * Tidak ada koneksi ke gateway sungguhan — langsung dianggap berhasil.
     */
    public function processPayment(Payment $payment, string $method): Payment
    {
        $payment->update([
            'method' => $method,
        ]);

        return $this->markPaid($payment);
    }

    /**
     * Tandai payment sebagai lunas.
     * Dipanggil oleh callback gateway / processPayment / verifikasi manual.
     *
     * Catatan: Item.availability sengaja TIDAK diubah di sini. Item tetap
     * terlihat di katalog & bisa dibooking untuk tanggal lain — konflik
     * tanggal dicek lewat Item::overlapsBookedDates().
     */
    public function markPaid(Payment $payment, ?string $transactionId = null): Payment
    {
        $payment->update([
            'status'         => Payment::STATUS_PAID,
            'transaction_id' => $transactionId ?? $payment->transaction_id ?? $this->generateTransactionId(),
            'paid_at'        => now(),
        ]);

        return $payment;
    }

    /**
     * Tandai payment gagal. Item tetap available (kalau belum pernah paid).
     */
    public function markFailed(Payment $payment): Payment
    {
        $payment->update([
            'status'    => Payment::STATUS_FAILED,
            'failed_at' => now(),
        ]);

        return $payment;
    }

    /**
     * Refund payment. Item.availability tidak diubah — vendor yang
     * mengontrol toggle availability secara manual.
     */
    public function refund(Payment $payment): Payment
    {
        $payment->update([
            'status'      => Payment::STATUS_REFUNDED,
            'refunded_at' => now(),
        ]);

        return $payment;
    }

    /**
     * Placeholder untuk integrasi gateway sungguhan (Midtrans, Xendit, dll).
     */
    private function generateTransactionId(): string
    {
        return 'TRX-' . strtoupper(Str::random(10));
    }
}
