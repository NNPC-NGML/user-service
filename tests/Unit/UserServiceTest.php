<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Tests\TestCase;
use App\Service\UserService\DepartmentHelper;

class UserServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_if_department_is_created(): void
    {
        $departmentHelper = new DepartmentHelper();
        $data_array = ['name'=>'department_name','description'=>'description'];
        $data = new Request($data_array);
        $this->assertTrue($departmentHelper->save($data));
    }
}
