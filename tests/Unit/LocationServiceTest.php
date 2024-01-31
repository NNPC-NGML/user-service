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
}
