<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use App\Models\HeadOfUnit;


class HeadOfUnitControllerTest extends TestCase
{
    use RefreshDatabase;



    /** @test */
    public function it_should_return_all_head_of_units()
    {
        // Arrange: Mock the service response
        HeadOfUnit::factory(3)->create();


        // Act: Make a GET request to the index route
        $response = $this->actingAsTestUser()->getJson('/api/v1/headofunit');
        // Assert: Check the response and resource collection
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_should_create_a_new_head_of_unit_successfully()
    {
        // Arrange: Set up the request data and mock service behavior
        $requestData = [
            'user_id' => 1,
            'unit_id' => 1,
            'location_id' => 1,
            'status' => 1,
        ];
        // Act: Make a POST request to the store route
        $response = $this->actingAsTestUser()->postJson('/api/v1/headofunit/create', $requestData);
        //dd($response);
        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'user_id', 'unit_id', 'location_id']]);
    }

    /** @test */
    public function it_should_handle_exception_during_creation()
    {
        // Arrange: Mock the request data and force an exception in the service
        $requestData = [];

        // Act: Make a POST request to the store route
        $response = $this->actingAsTestUser()->postJson('/api/v1/headofunit/create', $requestData);

        $response->assertJson([
            "data" => [
                "user_id" => [
                    "The user id field is required."
                ],
                "unit_id" => [
                    "The unit id field is required."
                ],
                "location_id" => [
                    "The location id field is required."
                ]
            ]
        ]);
    }

    /** @test */
    public function it_should_show_head_of_unit_by_id()
    {
        // Arrange: Mock the service to return a HeadOfUnit for a valid ID
        $headOfUnit = HeadOfUnit::factory()->create();

        // Act: Make a GET request to the show route
        $response = $this->actingAsTestUser()->getJson("/api/v1/headofunit/view/{$headOfUnit->id}");

        // Assert: Check that the correct HeadOfUnit is returned
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'user_id', 'unit_id', 'location_id']]);
    }

    /** @test */
    public function it_should_return_400_if_head_of_unit_not_found()
    {
        // Arrange: Mock the service to return null for a non-existent ID


        // Act: Make a GET request to the show route with a non-existent ID
        $response = $this->actingAsTestUser()->getJson('/api/v1/headofunit/view/999');
        // Assert: The response should return a 404 with an error message
        $response->assertStatus(400)
            ->assertJson(['status' => 'error', 'message' => 'could not fetch head of unit']);
    }
}
