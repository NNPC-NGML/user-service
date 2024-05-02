<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Location;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_deletes_location_if_exists()
    {
        $location = Location::factory()->create();

        $response = $this->actingAsTestUser()->deleteJson(route('locations.delete', ['id' => $location->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Location deleted successfully']);
    }

    /** @test */
    public function it_returns_error_if_location_not_found()
    {
        $response = $this->actingAsTestUser()->deleteJson(route('locations.delete', ['id' => 999]));

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Location not found']);
    }

    public function test_can_get_all_locations()
    {
        $data = [
            'location' => 'location1',
            'state' => 1,
            'zone' => 'zone1',
        ];

        Location::create($data);

        $response = $this->actingAsTestUser()->get(route('locations.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'zone',
                    'state'
                ]
            ]
        ]);

        $response->assertJsonCount(1, 'data');
    }

    public function test_get_all_locations_returns_no_data()
    {
        $response = $this->actingAsTestUser()->get(route('locations.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data'
        ]);

        $response->assertJson([
            'success' => true,
            'data' => []
        ]);
    }

    /** @test */
    public function test_show_location_exists()
    {

        $data = [
            'location' => 'location1',
            'state' => 1,
            'zone' => 'zone1',
        ];

        $location = Location::create($data);

        $response = $this->actingAsTestUser()->getJson(route('locations.show', ['locationId' => $location->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $location->id,
                'state' => $location->state,
                'zone' => $location->zone,
                'location' => $location->location
            ]
        ]);
    }

    /** @test */
    public function test_show_location_not_found()
    {
        $nonExistingLocationId = mt_rand(1000000000, 9999999999);


        $response = $this->actingAsTestUser()->getJson(route('locations.show', ['locationId' => $nonExistingLocationId]));

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'Location not found'
        ]);
    }

    /** @test */
    public function testCreateLocation()
    {
        $data = [
            'location' => 'Downtown',
            'zone' => 'Commercial',
            'state' => 1
        ];

        $response = $this->actingAsTestUser()->postJson(route('locations.create', $data));

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

        $response = $this->actingAsTestUser()->postJson(route('locations.create', $data));

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
    /** @test */
    public function testUpdateLocationSuccessfully()
    {
        $data_array = ['location' => 'location1', 'state' => 1, 'zone' => 'zone1'];

        $location = Location::create($data_array);

        $response = $this->actingAsTestUser()->patchJson(route('locations.update', $location->id), [
            'location' => 'location1',
            'state' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'location' => 'location1',
            'state' => 2,
        ]);
    }

    /** @test */
    public function testUpdateLocationValidationErrors()
    {
        $data_array = ['location' => 'location1', 'state' => 1, 'zone' => 'zone1'];

        $location = Location::create($data_array);


        $response = $this->actingAsTestUser()->patchJson(route('locations.update', $location->id), [
            'location' => '',
        ]);

        $response->assertStatus(500);
    }
}