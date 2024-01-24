<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Tests\TestCase;
use App\Service\DepartmentService;

class DepartmentServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_if_department_is_created(): void
    {
        $departmentService = new DepartmentService();
        $data_array = ['name'=>'department_name','description'=>'description'];
        $data = new Request($data_array);
        $this->assertTrue($departmentService->create($data));
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
