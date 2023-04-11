<?php

namespace App\Http\Repositories;

use App\Http\Requests\Loan\LoanListRequest;
use App\Models\Loan;

interface LoanRepository
{
    public function index(LoanListRequest $request);

    /**
     * Create a loan
     * @param float $amount
     * @param int $term
     * @return mixed
     */
    public function create(float $amount, int $term): Loan;

    /**
     * Approve a loan
     * @param Loan $loan
     * @return mixed
     */
    public function approve(Loan $loan): Loan;

    /**
     * Repayment a loan
     * @param Loan $loan
     * @param int $amount
     * @return Loan
     */
    public function repayment(Loan $loan, int $amount): Loan;

}
