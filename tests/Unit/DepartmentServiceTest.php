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
        $data_array = ['name' => 'department_name', 'description' => 'description'];
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
        $data_array = ['name' => 'department_name2'];
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

    public function test_to_view_all_department(): void
    {
        $departmentService = new DepartmentService();
        $data = new Request([
            "name" => "department name",
            "description" => "description goes here",
        ]);


        $departmentService->create($data);

        $fetchAllDepartment = $departmentService->viewAllDepartment();
        $this->assertCount(
            1,
            $fetchAllDepartment->toArray(),
            "FetchAllDepartment Array doesn't return the correct data count"
        );

    }


    public function test_to_see_if_an_existing_department_can_be_updated(): void
    {
        department::factory(5)->create();
        $newDepartmentservice = new DepartmentService();
        $this->assertDatabaseCount("departments", 5);
        $fetchService = $newDepartmentservice->getDepartment(1);
        //dd($fetchService);
        $data = new Request([
            "name" => "New Department Updated",
            "description" => "Description goes here",
        ]);
        $newDepartmentservice->updateDepartment($fetchService->id, $data);
        $this->assertDatabaseHas('departments', $data->all());
    }

    public function test_to_see_if_exception_would_be_thrown_if_there_is_an_error(): void
    {
        $this->expectException(\Exception::class);
        $newDepartmentService = new DepartmentService();
        $data = new Request([
            "name" => "Update Department",
            "description" => "Updated description",
        ]);

        $newDepartmentService->updateDepartment(1, $data);
        $this->expectExceptionMessage('Something went wrong.');
    }


    public function test_to_see_if_a_department_is_deleted()
    {
        $data = new Request([
            "name" => "department name",
            "description" => "description",

        ]);

        $departmentService = new DepartmentService();
        $data = $departmentService->create($data);
        $this->assertDatabaseCount("departments", 1);
        $delete = $departmentService->deleteDepartment($data->id);
        $this->assertDatabaseMissing("departments", ["name" => "department name"]);
        $this->assertTrue($delete);

    }


    public function test_to_see_if_there_is_no_record_with_the_provided_department_id()
    {
        $departmentService = new DepartmentService();
        $delete = $departmentService->deleteDepartment(5);
        $this->assertFalse($delete);

    }

    public function test_route_to_view_all_departments()
    {
        $response = $this->get('/api/v1/department')
            ->assertStatus(201)
            ->assertJsonStructure(
                [
                    'success',
                    'data' => [
                    ]
                ]
            );
    }

}