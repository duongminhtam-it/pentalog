<?php

namespace Tests\Feature\Controller\Loan;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoanCreateTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'loan.create';

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public static function createLoanDataProvider(): array
    {
        return [
            [['amount' => 10000, 'term' => 3], Response::HTTP_OK],
            [['amount' => 10001, 'term' => 3], Response::HTTP_OK],
            [['amount' => 10002, 'term' => 3], Response::HTTP_OK],
            [['amount' => 5555, 'term' => 2], Response::HTTP_OK],
            [['amount' => 1, 'term' => 3], Response::HTTP_OK],
            [['amount' => 0, 'term' => 3], Response::HTTP_UNPROCESSABLE_ENTITY],
            [['amount' => 0, 'term' => 0], Response::HTTP_UNPROCESSABLE_ENTITY],
            [['term' => 3], Response::HTTP_UNPROCESSABLE_ENTITY],
            [['amount' => 0], Response::HTTP_UNPROCESSABLE_ENTITY],
            [[], Response::HTTP_UNPROCESSABLE_ENTITY],
        ];
    }

    /**
     * @dataProvider createLoanDataProvider
     * @param array $data
     * @param int $expectedCode
     * @return void
     */
    public function testCreateLoan(array $data, int $expectedCode): void
    {
        $response = $this->postJson(route($this->route), $data);
        $response->assertStatus($expectedCode);
        $result = $response->json('data');
        if ($expectedCode == Response::HTTP_OK) {
            $this->assertCount($data['term'],$result['schedules']);
            $this->assertEquals($data['amount'], array_sum(array_column($result['schedules'], 'amount')));
        }
    }
}
