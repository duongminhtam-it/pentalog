<?php

namespace App\Http\Controllers;

use App\Http\Repositories\LoanRepository;
use App\Http\Requests\Loan\LoanCreateRequest;
use App\Http\Requests\Loan\LoanListRequest;
use App\Http\Requests\Loan\LoanRepaymentRequest;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    public function __construct(
        protected LoanRepository $loanRepository,
    )
    {
    }

    /**
     * Get list loan of user
     * @param LoanListRequest $request
     * @return JsonResponse
     */
    public function index(LoanListRequest $request): JsonResponse
    {
        try {
            $result = $this->loanRepository->index($request);
            return $this->success($result);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            return $this->error();
        }
    }

    /**
     * Get a loan
     * @param Loan $loan
     * @return JsonResponse
     */
    public function view(Loan $loan): JsonResponse
    {
        try {
            return $this->success($loan->load(['schedules', 'schedules.repayments']));
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            return $this->error();
        }
    }

    /**
     * Create a loan
     * @param LoanCreateRequest $request
     * @return JsonResponse
     */
    public function create(LoanCreateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $result = $this->loanRepository->create($validated['amount'], $validated['term']);
            return $this->success($result);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            return $this->error($throwable->getMessage());
        }
    }

    /**
     * Approve a loan
     * @param Loan $loan
     * @return JsonResponse
     */
    public function approve(Loan $loan): JsonResponse
    {
        try {
            $result = $this->loanRepository->approve($loan);
            return $this->success($result);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            return $this->error($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * Repayment a loan
     * @param LoanRepaymentRequest $request
     * @param Loan $loan
     * @return JsonResponse
     */
    public function repayment(LoanRepaymentRequest $request, Loan $loan): JsonResponse
    {
        try {
            $validated = $request->validated();
            $result = $this->loanRepository->repayment($loan, $validated['amount']);
            return $this->success($result);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            return $this->error($throwable->getMessage(), $throwable->getCode());
        }
    }

}
