<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\Returning;

class DepositService
{
    /**
     * Calculate how much of the deposit to refund after return verification.
     *
     * Rules:
     *  - Good condition + on time  → full deposit refund
     *  - Good condition + overdue  → 20% deducted
     *  - Damaged                   → deposit fully withheld
     *
     * @return float  Amount to refund to the user
     */
    public function calculateRefund(Rental $rental): float
    {
        $deposit   = $rental->adjustedDeposit();
        $returning = $rental->returning;

        if ($returning->isDamaged()) {
            return 0.0;
        }

        // Good condition — check timeliness
        $expectedReturn = $rental->expectedReturnDate();
        $returnedAt     = $returning->updated_at ?? now();

        if ($returnedAt->isAfter($expectedReturn)) {
            // Late return: deduct 20%
            return round($deposit * 0.8, 2);
        }

        return (float) $deposit;
    }
}
