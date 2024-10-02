<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationUserTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test that the /api/auth/initialize route returns a 302 redirect to Microsoft OAuth page.
     *
     * @return void
     */
    public function test_initialize_redirects_to_microsoft_oauth()
    {
        // Simulate a request to the initialize route
        $response = $this->getJson('/api/auth/initialize');

        // Assert that the response is a redirect
        $response->assertStatus(302);

        // Assert that the redirect URL contains "login.microsoftonline.com"
        $this->assertStringContainsString(
            'login.microsoftonline.com',
            $response->headers->get('Location') // Check the "Location" header for the redirect
        );
    }

    // /**
    //  * Test that the /api/auth/callback route handles Microsoft OAuth callback and returns user data.
    //  *
    //  * @return void
    //  */
    // public function test_callback_creates_or_fetches_user()
    // {
    //     // Mock the Socialite User
    //     $socialiteUser = Mockery::mock(SocialiteUser::class);
    //     $socialiteUser->shouldReceive('getEmail')->andReturn('testuser@example.com');
    //     $socialiteUser->shouldReceive('getName')->andReturn('Test User');
    //     $socialiteUser->shouldReceive('getId')->andReturn('azure-1234');

    //     // Mock the Socialite driver to return the mocked user
    //     Socialite::shouldReceive('driver')
    //         ->with('microsoft')
    //         ->andReturn(Mockery::mock([
    //             'stateless' => Mockery::self(),
    //             'user' => $socialiteUser,
    //         ]));

    //     // Call the callback route
    //     $response = $this->postJson('/api/auth/callback');

    //     // Assert that the user was created in the database
    //     $this->assertDatabaseHas('users', [
    //         'email' => 'testuser@example.com',
    //         'name' => 'Test User',
    //         'azure_id' => 'azure-1234',
    //     ]);

    //     // Fetch the created user
    //     $user = User::where('email', 'testuser@example.com')->first();

    //     // Assert that the response contains the user information
    //     $response->assertStatus(201);
    //     $response->assertJson([
    //         'message' => 'User registered successfully',
    //         'user' => [
    //             'id' => $user->id,
    //             'name' => 'Test User',
    //             'email' => 'testuser@example.com',
    //             'azure_id' => 'azure-1234',
    //             'status' => 1,
    //         ],
    //     ]);
    // }

    public function test_it_can_register_a_new_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(201);

        // $response->assertStatus(201)
        //     ->assertJsonStructure(['name', 'email']);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function test_it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'jwt']);
    }

    /** @test */
    public function test_it_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $loginData = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson(['error' => 'invalid credentials']);
    }
}
