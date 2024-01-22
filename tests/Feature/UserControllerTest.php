<?php

// tests/Feature/UserControllerTest.php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_user()
    {
        // Mock Data
        $data_array = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'password' => 'password123',
        ];

        // MOCK API CALL
        $response = $this->post(route('create_user'), $data_array);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        // check if the user password is hashed
        $this->assertTrue(Hash::check('password123', User::first()->password));
    }
}

