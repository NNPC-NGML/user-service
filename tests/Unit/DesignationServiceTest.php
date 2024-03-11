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



    public function test_to_see_if_an_existing_designation_can_be_updated(): void
    {
        Designation::factory(5)->create();
        $newDesignationService = new DesignationService();
        $fetchService = $newDesignationService->getDesignation(1);
        $this->assertDatabaseCount("designations", 5);
        $data = new Request([
            "role" => "New role Updated",
            "description" => "Description goes here",
        ]);
        $newDesignationService->updateDesignation($fetchService->id, $data);
        $this->assertDatabaseHas('designations', $data->all());
    }

    public function test_to_see_if_exception_would_be_thrown_if_there_is_an_error(): void
    {
        $this->expectException(\Exception::class);
        $newDesignationService = new DesignationService();
        $data = new Request([
            "role" => "Update role",
            "description" => "Updated description",
        ]);

        $newDesignationService->updateDesignation(1, $data);
        $this->expectExceptionMessage('Something went wrong.');
    }
}
