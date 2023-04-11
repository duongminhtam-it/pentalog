<?php

namespace tests\Feature\Controller\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function testRegisterSuccess(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 123456,
            'c_password' => 123456,
        ];
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertSuccessful();
    }
}
