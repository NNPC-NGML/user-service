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
    public function testUpdateLocationSuccessfully()
    {
        $data_array = ['location'=>'location1','state'=>1,'zone'=>'zone1'];

        $location = Location::create($data_array);

        $response = $this->patchJson(route('locations.update', $location->id), [
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
        $data_array = ['location'=>'location1','state'=>1,'zone'=>'zone1'];

        $location = Location::create($data_array);


        $response = $this->patchJson(route('locations.update', $location->id), [
            'location' => '',
        ]);

        $response->assertStatus(500);

    }
}
