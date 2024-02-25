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
}
