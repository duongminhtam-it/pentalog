<?php

namespace App\Http\Repositories;

use App\Http\Requests\Loan\LoanListRequest;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Response;

class LoanRepositoryImpl implements LoanRepository
{
    /**
     * Get loan list
     * @param LoanListRequest $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(LoanListRequest $request)
    {
        $auth = Auth::user();
        $loan = Loan::query();

        if ($request->has('status')) {
            $loan->where('status', '=', $request->input('status'));
        }

        if (!$auth->is_admin) {
            $loan->where('user_id', '=', $auth->id);
        } elseif ($request->has('user_id')) {
            $loan->where('user_id', '=', $request->input('user_id'));
        }

        return $loan->paginate($request->input('limit', 50));
    }

    /**
     * Create a loan
     * @param float $amount
     * @param int $term
     * @return Loan
     */
    public function create(float $amount, int $term): Loan
    {
        // create loan
        /** @var Loan $loan */
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'amount' => $amount,
            'term' => $term,
            'date' => date('Y-m-d'),
            'status' => Loan::STATUS_PENDING,
        ]);

        // create schedule
        $weeklyAmount = round($amount / $term, 2);
        $dayRange = 7;
        while ($term > 0) {
            Schedule::create([
                'loan_id' => $loan->id,
                'amount' => $term == 1 ? $amount : $weeklyAmount,
                'date' => date('Y-m-d', strtotime(" + $dayRange days")),
                'status' => Schedule::STATUS_UNPAID,
            ]);
            $amount -= $weeklyAmount;
            $dayRange += $dayRange;
            $term--;
        }

        return $loan->load(['schedules']);
    }

    /**
     * Approve a loan
     * @param Loan $loan
     * @return Loan
     */
    public function approve(Loan $loan): Loan
    {
        if ($loan->status != Loan::STATUS_PENDING) {
            throw new Exception('Loan status should be pending', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $loan->update(['status' => Loan::STATUS_APPROVED]);

        return $loan->load(['schedules']);
    }

    /**
     * Repayment a loan
     * @param Loan $loan
     * @param int $amount
     * @return Loan
     */
    public function repayment(Loan $loan, int $amount): Loan
    {
        if ($loan->status == Loan::STATUS_PENDING) {
            throw new Exception('Loan status should be approve', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // get next schedule
        $schedule = $loan->schedules()
            ->where('status', '<>', Schedule::STATUS_PAID)
            ->first();

        if (!$schedule) {
            $loan->update(['status' => Loan::STATUS_PAID]);
        } elseif ($amount > 0) {
            // check remain amount
            $remain = $schedule->amount - $schedule->paid;
            if ($amount >= $remain) {
                // full pay the schedule and continue pay next schedule if remain
                $schedule->update([
                    'paid' => $schedule->amount,
                    'status' => Schedule::STATUS_PAID
                ]);
                Repayment::create([
                    'schedule_id' => $schedule->id,
                    'amount' => $remain,
                    'date' => date('Y-m-d'),
                ]);
                return $this->repayment($loan->refresh(), $amount - $remain);
            } else {
                // partially pay the schedule
                $schedule->update([
                    'paid' => $schedule->paid + $amount,
                    'status' => Schedule::STATUS_PARTIALLY_PAID
                ]);
                Repayment::create([
                    'schedule_id' => $schedule->id,
                    'amount' => $amount,
                    'date' => date('Y-m-d'),
                ]);
            }
        }

        return $loan->refresh()->load(['schedules', 'schedules.repayments']);
    }
}
