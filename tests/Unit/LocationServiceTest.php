<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Service\LocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_if_location_is_created(): void
    {
        $departmentService = new LocationService();
        $data_array = ['location'=>'location1','state'=>'state1','zone'=>'zone1'];
        $data = new Request($data_array);
        $result = $departmentService->create($data);
        $this->assertInstanceOf(Location::class, $result);
        $this->assertNotNull($result->id);
        $this->assertDatabaseHas('locations', [
            'location'=>'location1','state'=>'state1','zone'=>'zone1'
        ]);        
    }

    public function test_if_location_is_not_created(): void
    {
        $locationService = new LocationService();
        $data_array = ['location'=>'new location'];
        $data = new Request($data_array);
        $createDepartment = $locationService->create($data);
        $resultArray = $createDepartment->toArray();
        $this->assertNotEmpty($createDepartment);
        $this->assertIsArray($resultArray);
        $this->assertArrayHasKey('state', $resultArray);
    }


    public function test_to_see_if_a_location_can_be_fetched(): void
    {
        $data = new Request([
            "location" => "Location1",
            "zone" => "Zone1",
            "state" => "State1",
        ]);

        $location = new LocationService();
        $result = $location->create($data);
        $fetchService = $location->getLocation($result->id);
        $this->assertEquals($fetchService->id, $result->id);
        $this->assertSame('Location1', $fetchService->location);
        $this->assertSame('Zone1', $fetchService->zone);
        $this->assertInstanceOf(Location::class, $fetchService);

    }

    public function test_to_see_if_location_returns_null_when_there_is_no_data(): void
    {
        $locationService = new LocationService();
        $fetchService = $locationService->getLocation(2);
        $this->assertNull($fetchService);
        //dd($fetchService);
    }

}
