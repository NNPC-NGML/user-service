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

    public function test_to_see_if_an_existing_location_can_be_updated(): void
    {
        Location::factory(5)->create();
        $locationService = new LocationService();
        $fetchService = $locationService->getLocation(1);
        $this->assertDatabaseCount("locations", 5);
        $data = new Request([
            "location" => "Location1",
            "zone" => "Zone1",
            "state" => "State1",
        ]);
        $locationService->updateLocation($fetchService->id, $data);
        $this->assertDatabaseHas('locations', $data->all());
    }

    public function test_to_see_if_exception_would_be_thrown_if_there_is_an_error(): void
    {
        $this->expectException(\Exception::class);
        $locationService = new LocationService();
        $data = new Request([
            "location" => "Update Location",
            "state" => "Updated state",
        ]);

        $locationService->updateLocation(1, $data);
        $this->expectExceptionMessage('Something went wrong.');
    }

    public function test_to_see_if_location_returns_all_records(): void
    {
        Location::factory(15)->create();
        $locationService = new LocationService();
        $fetchAllLocations = $locationService->viewAllLocations();
        $this->assertCount(
            15,
            $fetchAllLocations->toArray(), "FetchAllLocation Array doesn't return the correct data count"
        );

    }


    public function test_to_see_if_a_location_is_deleted()
    {
        $data = new Request([
            "location" => "Location1",
            "state" => "State1",
            'zone'=>'zone1'
        ]);
        $locationService = new LocationService();
        $data = $locationService->create($data);
        $this->assertDatabaseCount("locations", 1);
        $delete = $locationService->deleteLocation($data->id);
        $this->assertDatabaseMissing("locations", ["location" => "Location1"]);
        $this->assertTrue($delete);

    }


    public function test_to_see_if_there_is_no_record_with_the_provided_department_id()
    {
        $locationService = new LocationService();
        $delete = $locationService->deleteLocation(5);
        $this->assertFalse($delete);

    }


}
