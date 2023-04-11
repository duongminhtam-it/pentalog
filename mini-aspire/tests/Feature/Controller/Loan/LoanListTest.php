<?php

namespace Tests\Feature\Controller\Loan;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanListTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'loan.index';

    public static function listLoanDataProvider(): array
    {
        return [
            [0, null, null, 21],
            [0, 1, null, 6],
            [0, 1, 0, 1],
            [0, 1, 1, 2],
            [0, 1, 2, 3],
            [0, 2, null, 15],
            [0, 2, 0, 4],
            [0, 2, 1, 5],
            [0, 2, 2, 6],
            [1, 1, null, 6],
            [1, 1, 0, 1],
            [1, 1, 1, 2],
            [1, 1, 2, 3],
            [2, 2, null, 15],
            [2, 2, 0, 4],
            [2, 2, 1, 5],
            [2, 2, 2, 6],
        ];
    }

    /**
     * @dataProvider listLoanDataProvider
     * @param int $authIndex
     * @param int|null $userIndex
     * @param int|null $status
     * @param int $expectedCount
     * @return void
     */
    public function testListLoan(int $authIndex, ?int $userIndex, ?int $status, int $expectedCount = 0): void
    {
        Loan::truncate();
        $users = [
            0 => User::factory()->admin()->create(),
            1 => User::factory()->create(),
            2 => User::factory()->create(),
        ];
        Loan::factory(1)->pending()->for($users[1])->create();
        Loan::factory(2)->approved()->for($users[1])->create();
        Loan::factory(3)->paid()->for($users[1])->create();
        Loan::factory(4)->pending()->for($users[2])->create();
        Loan::factory(5)->approved()->for($users[2])->create();
        Loan::factory(6)->paid()->for($users[2])->create();
        Sanctum::actingAs($users[$authIndex]);

        $params = [];
        if (!is_null($userIndex)) {
            $params['user_id'] = $users[$userIndex]->id;
        }
        if (!is_null($status)) {
            $params['status'] = $status;
        }

        $response = $this->getJson(route($this->route, $params));
        $response
            ->assertSuccessful()
            ->assertJsonCount($expectedCount, 'data.data');
    }
}
