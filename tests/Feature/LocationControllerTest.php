<?php

use Tests\TestCase;
use App\Models\Location;

class LocationControllerTest extends TestCase
{
    /** @test */
    public function it_deletes_location_if_exists()
    {
        $location = Location::factory()->create();

        $response = $this->deleteJson(route('locations.delete', ['id' => $location->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Location deleted successfully']);
    }

    /** @test */
    public function it_returns_error_if_location_not_found()
    {
        $response = $this->deleteJson(route('locations.delete', ['id' => 999]));

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Location not found']);
    }
    /** @test */
    public function testCreateLocation()
    {
        $data = [
            'location' => 'Downtown',
            'zone' => 'Commercial',
            'state' => 1
        ];

        $response = $this->postJson(route('locations.create', $data));

        $response->assertStatus(201);

        $this->assertDatabaseHas('locations', $data);
    }
    /** @test */
    public function testValidationErrors()
    {
        $data = [
            'location' => '',
            'zone' => '',
            'state' => ''
        ];

        $response = $this->postJson(route('locations.create', $data));

        $response->assertStatus(422);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'location' => ['The location field is required.'],
                    'zone' => ['The zone field is required.'],
                    'state' => ['The state field is required.'],
                ]
            ]);
    }
}
