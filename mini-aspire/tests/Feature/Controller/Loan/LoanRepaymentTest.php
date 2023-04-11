<?php

namespace Tests\Feature\Controller\Loan;

use App\Models\Loan;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoanRepaymentTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'loan.repayment';

    public static function repaymentLoanDataProvider(): array
    {
        return [
            [[[3000, Response::HTTP_OK], [3000, Response::HTTP_OK], [3000, Response::HTTP_OK]], 3000, 3],
            [[[4000, Response::HTTP_OK], [4000, Response::HTTP_OK], [4000, Response::HTTP_OK]], 3000, 3],
            [[[2000, Response::HTTP_UNPROCESSABLE_ENTITY], [4000, Response::HTTP_OK], [4000, Response::HTTP_OK], [3000, Response::HTTP_OK]], 3000, 3],
        ];
    }

    /**
     * @dataProvider repaymentLoanDataProvider
     * @param array $repaymentData
     * @param float $scheduleAmount
     * @param int $term
     * @return void
     */
    public function testRepaymentLoan(
        array $repaymentData,
        float $scheduleAmount,
        int   $term,
    ): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $loan = Loan::factory()
            ->state(['status' => Loan::STATUS_APPROVED, 'amount' => $scheduleAmount * $term])
            ->for($user)
            ->has(
                Schedule::factory($term)->state(['amount' => $scheduleAmount])
            )->create();

        foreach ($repaymentData as $data) {
            $response = $this->postJson(route($this->route, $loan->id), ['amount' => $data[0]]);
            $response->assertStatus($data[1]);
        }

        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PAID]);
        $this->assertDatabaseMissing('schedules', ['loan_id' => $loan->id, 'status' => Schedule::STATUS_PARTIALLY_PAID]);
        $this->assertDatabaseMissing('schedules', ['loan_id' => $loan->id, 'status' => Schedule::STATUS_UNPAID]);
    }
}
