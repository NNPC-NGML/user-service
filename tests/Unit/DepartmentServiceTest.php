<?php

namespace Tests\Unit;

use App\Models\department;
use Tests\TestCase;
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
        dd($resultArray);
    }
}
