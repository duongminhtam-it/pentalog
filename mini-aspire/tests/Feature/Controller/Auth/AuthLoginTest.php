<?php

namespace Tests\Feature\Controller\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'auth.login';

    public function loginDataProvider(): array
    {
        return [
            ['valid@gmail.com', 'valid', Response::HTTP_OK],
            ['gmail.com', 'valid', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['', 'valid', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['test@gmail.com', '', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['test@gmail.com', 'vali', Response::HTTP_UNAUTHORIZED],
        ];
    }

    /**
     * @dataProvider loginDataProvider
     * @param string $email
     * @param string $password
     * @param int $expectedCode
     * @return void
     */
    public function testLogin(string $email, string $password, int $expectedCode): void
    {
        // create valid account
        User::create([
            'name' => 'Valid',
            'email' => 'valid@gmail.com',
            'password' => bcrypt('valid'),
            'is_admin' => true,
        ]);

        $data = [
            'email' => $email,
            'password' => $password,
        ];
        $response = $this->postJson(route($this->route), $data);
        $response->assertStatus($expectedCode);
        if ($expectedCode == Response::HTTP_OK) {
            $data = $response->json('data');
            $this->assertArrayHasKey('token', $data);
            $this->assertArrayHasKey('name', $data);
        }
    }
}
