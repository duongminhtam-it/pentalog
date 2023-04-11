<?php

namespace Tests\Feature\Controller\Loan;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoanViewTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'loan.view';

    public static function viewLoanDataProvider(): array
    {
        return [
            [true, true, Response::HTTP_OK],
            [true, false, Response::HTTP_OK],
            [false, true, Response::HTTP_OK],
            [false, false, Response::HTTP_FORBIDDEN]
        ];
    }

    /**
     * @dataProvider viewLoanDataProvider
     * @param bool $isAdmin
     * @param bool $isOwner
     * @param int $expectedCode
     * @return void
     */
    public function testViewLoan(bool $isAdmin, bool $isOwner, int $expectedCode): void
    {
        $ownerFactory = User::factory();
        if ($isAdmin) {
            $ownerFactory = $ownerFactory->admin();
        }
        $owner = $ownerFactory->create();
        Sanctum::actingAs($owner);
        if ($isOwner) {
            $loan = Loan::factory()->for($owner)->create();
        } else {
            $loan = Loan::factory()->for(User::factory()->create())->create();
        }
        $response = $this->getJson(route($this->route, $loan->id));
        $response->dump();
        $response->assertStatus($expectedCode);
        if ($expectedCode == Response::HTTP_OK) {
            $this->assertEquals($loan->id, $response->json('data.id'));
        }
    }
}
