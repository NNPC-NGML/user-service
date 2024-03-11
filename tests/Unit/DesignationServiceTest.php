<?php

namespace Tests\Unit;
use Tests\TestCase;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Service\DesignationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DesignationServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_if_designation_is_created(): void
    {
        $designationService = new DesignationService();
        $data_array = ['role'=>'role name','description'=>'role description','level'=>'10'];
        $data = new Request($data_array);
        $result = $designationService->create($data);
        $this->assertInstanceOf(Designation::class, $result);
        $this->assertNotNull($result->id);
        $this->assertDatabaseHas('designations', [
            "role" => "role name",
            "description" => "role description",
            'level'=>'10'
        ]);
        //$this->assertSame('this should be a route', $result->step_route);
    }


    public function test_if_designation_is_not_created(): void
    {
        $designationService = new DesignationService();
        $data_array = ['role'=>'role name'];
        $data = new Request($data_array);
        $createDesignation = $designationService->create($data);
        $resultArray = $createDesignation->toArray();
        $this->assertNotEmpty($createDesignation);
        $this->assertIsArray($resultArray);
        $this->assertArrayHasKey('description', $resultArray);
        //dd($resultArray);
    }

    public function test_to_see_if_a_designation_can_be_fetched(): void
    {

        $data = new Request([
            "role" => "role name",
            "description" => "description goes here",
            "level" => "level 10",
        ]);

        $designation = new DesignationService();
        $result = $designation->create($data);
        $fetchService = $designation->getDesignation($result->id);
        $this->assertEquals($fetchService->id, $result->id);
        $this->assertSame('role name', $fetchService->role);
        $this->assertSame('description goes here', $fetchService->description);
        $this->assertSame('level 10', $fetchService->level);
        $this->assertInstanceOf(Designation::class, $fetchService);

    }

    public function test_to_see_if_designation_returns_null_when_there_is_no_data(): void
    {
        $designationService = new DesignationService();
        $fetchService = $designationService->getDesignation(2);
        $this->assertNull($fetchService);
        //dd($fetchService);
    }
}
