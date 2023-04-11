<?php

namespace Tests\Feature\Controller\Loan;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoanApproveTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'loan.approve';

    public static function approveLoanDataProvider(): array
    {
        return [
            [true, Loan::STATUS_PENDING, Response::HTTP_OK],
            [true, Loan::STATUS_APPROVED, Response::HTTP_UNPROCESSABLE_ENTITY],
            [true, Loan::STATUS_PAID, Response::HTTP_UNPROCESSABLE_ENTITY],
            [false, Loan::STATUS_PENDING, Response::HTTP_FORBIDDEN],
            [false, Loan::STATUS_APPROVED, Response::HTTP_FORBIDDEN],
            [false, Loan::STATUS_PAID, Response::HTTP_FORBIDDEN],
        ];
    }

    /**
     * @dataProvider approveLoanDataProvider
     * @param bool $isAdmin
     * @param int $loanStatus
     * @param int $expectedCode
     * @return void
     */
    public function testApproveLoan(bool $isAdmin, int $loanStatus, int $expectedCode): void
    {
        $userFactory = User::factory();
        if ($isAdmin) {
            $userFactory = $userFactory->admin();
        }
        $user = $userFactory->create();
        Sanctum::actingAs($user);
        $loan = Loan::factory()->state(['status' => $loanStatus])->for($user)->create();
        $response = $this->patchJson(route($this->route, $loan->id));
        $response->dump();
        $response->assertStatus($expectedCode);
        if ($expectedCode == Response::HTTP_OK) {
            $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_APPROVED]);
        }
    }
}
