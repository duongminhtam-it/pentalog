<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    /**
     * Determine whether the user can view the loan.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->is_admin || $loan->user_id == $user->id;
    }

    /**
     * Determine whether the user can approve the loan.
     */
    public function approve(User $user, Loan $loan): bool
    {
        return $user->is_admin ?? false;
    }

    /**
     * Determine whether the user can repayment the loan.
     */
    public function repayment(User $user, Loan $loan): bool
    {
        return $user->is_admin || $loan->user_id == $user->id;
    }
}
