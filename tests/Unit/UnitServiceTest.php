<?php

namespace Tests\Unit;

use App\Models\department;
use Tests\TestCase;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Service\UnitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;

class UnitServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    /**
     * Test creating a unit successfully.
     */
    public function testCreateUnit(): void
    {
        // Create a department for testing
        $department = department::factory()->create();

        $unitData = [
            'name' => 'Test Unit',
            'description' => 'Description of the test unit.',
            'departmentId' => $department->id,
        ];

        $request = new Request($unitData);
        $unitService = new UnitService();
        $createdUnit = $unitService->create($request);

        $this->assertInstanceOf(Unit::class, $createdUnit);
        $this->assertEquals('Test Unit', $createdUnit->name);
        $this->assertEquals('Description of the test unit.', $createdUnit->description);
        $this->assertEquals($department->id, $createdUnit->department_id);

        // Check if the unit record exists in the database
        $this->assertDatabaseHas('units', [
            'id' => $createdUnit->id,
            'name' => 'Test Unit',
            'description' => 'Description of the test unit.',
            'department_id' => $department->id,
        ]);
    }


    /**
     * Test creating a unit with invalid data.
     */
    public function testCreateUnitWithInvalidData(): void
    {
        $unitData = [
            // Missing required 'name' field
            'description' => 'Invalid unit description.',
            'departmentId' => 1,
        ];

        $request = new Request($unitData);
        $unitService = new UnitService();
        $createdUnit = $unitService->create($request);

        // Assert that the unit creation failed
        $this->assertInstanceOf(MessageBag::class, $createdUnit);

        $this->assertEquals(['The name field is required.'], $createdUnit->get('name'));


        // Check if the unit record does not exist in the database
        $this->assertDatabaseMissing('units', [
            'description' => 'Invalid unit description.',
        ]);
    }
    /**
     * Test getting all units in a department.
     */
    public function testGetUnitsInDepartment(): void
    {
        $department = department::factory()->create();

        // Create units in the department
        $unit1 = Unit::create([
            'name' => 'Unit 1',
            'description' => 'Description 1',
            'department_id' => $department->id,
        ]);

        $unit2 = Unit::create([
            'name' => 'Unit 2',
            'description' => 'Description 2',
            'department_id' => $department->id,
        ]);

        $unitService = new UnitService();
        $unitsInDepartment = $unitService->getUnitsInDepartment($department->id);

        // Assert that the correct units are retrieved
        $this->assertCount(2, $unitsInDepartment);
        $this->assertTrue($unitsInDepartment->contains($unit1));
        $this->assertTrue($unitsInDepartment->contains($unit2));
    }

    /**
     * Test getting units in a non-existent department.
     */
    public function testGetUnitsInNonExistentDepartment(): void
    {
        $nonExistentDepartmentId = mt_rand(1000000000, 9999999999);
        $unitService = new UnitService();
        $unitsInNonExistentDepartment = $unitService->getUnitsInDepartment($nonExistentDepartmentId);

        $this->assertCount(0, $unitsInNonExistentDepartment);
    }
}
