<?php

namespace Tests\Unit;

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
        $this->assertTrue($departmentService->create($data));
        $this->assertDatabaseHas('departments', [
            "name" => "department_name",
            "description" => "description",
        ]);
        
    }

    public function test_if_department_is_not_created(): void
    {
        $departmentService = new DepartmentService();
        $data_array = ['name'=>'department_name2','description'=>'description'];
        $data = new Request($data_array);
        $createDepartment = $departmentService->create($data);
        $this->assertFalse(!$createDepartment);
    }
}
