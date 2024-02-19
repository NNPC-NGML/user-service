<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $response = $this->post(route('create_user'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        // check if the user password is hashed
        $this->assertTrue(Hash::check('password123', User::first()->password));
    }

    /** @test */
    public function it_requires_email_and_password_for_user_creation()
    {
        $data = ['name' => 'John Doe'];

        $response = $this->postJson(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    }
    /** @test */
    public function it_validates_email_format_for_user_creation()
    {
        $data = [
            'email' => 'invalidemail',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $response = $this->post(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'email' => ['The email field must be a valid email address.'],
                ]
            ]);
    }
    /** @test */
    public function it_validates_unique_email_for_user_creation()
    {
        // Create a user with the same email first
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'email' => ['The email has already been taken.'],
                ]
            ]);
    }
    /** @test */
    public function it_validates_password_length_for_user_creation()
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => '123',
        ];

        $response = $this->postJson(route('create_user'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'password' => ['The password field must be at least 8 characters.'],
                ]
            ]);
    }
    /** @test */
    public function test_show_user_exists()
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('users.show', ['userId' => $user->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]
        ]);
    }
    /** @test */
    public function test_show_user_not_found()
    {
        $nonUserId = mt_rand(1000000000, 9999999999);


        $response = $this->getJson(route('users.show', ['userId' => $nonUserId]));

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'User not found'
        ]);
    }
}
