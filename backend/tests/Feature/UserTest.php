<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testHomepage()
    {
        $resp = $this->get('/');
        $resp->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testUserCreate()
    {
        $resp = $this->postJson('/api/v1/user', [
            'email' => 'test@example.com',
            'password' => '12345678'
        ]);
        $resp->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testErrorUserCreateAlreadyExists()
    {
        User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $resp = $this->postJson('/api/v1/user', [
            'email' => 'admin@example.com',
            'password' => '12345678'
        ]);
        $resp->assertStatus(422);
        $resp->assertExactJson([
            'status' => false,
            'response' => [
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]
        ]);
    }
}
