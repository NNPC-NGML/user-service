<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\department;
use Illuminate\Http\Request;
use App\Service\DepartmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_if_department_is_created(): void
    {
        $departmentService = new DepartmentService();
        $data_array = ['name'=>'department_name','description'=>'description'];
        $data = new Request($data_array);
        $result = $departmentService->create($data);
        $this->assertInstanceOf(department::class, $result);
        $this->assertNotNull($result->id);
        $this->assertDatabaseHas('departments', [
            "name" => "department_name",
            "description" => "description",
        ]);
        //$this->assertSame('this should be a route', $result->step_route);
        
    }

    public function test_if_department_is_not_created(): void
    {
        $departmentService = new DepartmentService();
        $data_array = ['name'=>'department_name2'];
        //$data_array = ['name'=>'department_name','description'=>'description'];
        $data = new Request($data_array);
        $createDepartment = $departmentService->create($data);
        $resultArray = $createDepartment->toArray();
        $this->assertNotEmpty($createDepartment);
        $this->assertIsArray($resultArray);
        //$this->assertArrayHasKey('name', $resultArray);
        $this->assertArrayHasKey('description', $resultArray);
        //dd($resultArray);
    }

    
    public function test_to_see_if_a_department_can_be_fetched(): void
    {

        $data = new Request([
            "name" => "department name",
            "description" => "description goes here",
        ]);

        $department = new DepartmentService();
        $result = $department->create($data);
        $fetchService = $department->getDepartment($result->id);
        $this->assertEquals($fetchService->id, $result->id);
        $this->assertSame('department name', $fetchService->name);
        $this->assertSame('description goes here', $fetchService->description);
        $this->assertInstanceOf(department::class, $fetchService);

    }

    public function test_to_see_if_department_returns_null_when_there_is_no_data(): void
    {
        $departmentService = new DepartmentService();
        $fetchService = $departmentService->getDepartment(2);
        $this->assertNull($fetchService);
        //dd($fetchService);
    }
}
