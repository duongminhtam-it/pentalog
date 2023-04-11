<?php

namespace tests\Feature\Controller\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use DatabaseTransactions;

    private $route = 'auth.register';

    public function registerDataProvider(): array
    {
        return [
            ['test', 'test@gmail.com', '123456', '123456', Response::HTTP_OK],
            ['test', 'test@gmail.com', '123456', '123', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['test', 'duplicate@gmail.com', '123456', '123456', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['', 'gmail.com', '123456', '123456', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['test', '', '123456', '123456', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['test', 'test@gmail.com', '', '123456', Response::HTTP_UNPROCESSABLE_ENTITY],
            ['test', 'test@gmail.com', '123456', '', Response::HTTP_UNPROCESSABLE_ENTITY],
        ];
    }

    /**
     * @dataProvider registerDataProvider
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $cPassword
     * @param int $expectedCode
     * @return void
     */
    public function testRegister(string $name, string $email, string $password, string $cPassword, int $expectedCode): void
    {
        // create duplicate mail
        User::create([
            'name' => 'Admin',
            'email' => 'duplicate@gmail.com',
            'password' => bcrypt('admin'),
            'is_admin' => true,
        ]);

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'c_password' => $cPassword,
        ];
        $response = $this->postJson(route($this->route), $data);
        $response->assertStatus($expectedCode);
    }
}
