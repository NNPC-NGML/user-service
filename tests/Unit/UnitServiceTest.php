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
    public function testUpdateUnitSuccessfully(): void
    {

        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Test Description',
            'department_id' => $department->id,
        ]);

        $updatedData = [
            'name' => 'Updated Unit Name',
            'description' => 'Updated Unit Description',
            'department_id' => $department->id,
        ];

        $unitService = new UnitService();
        $updatedUnit = $unitService->updateUnit($unit->id, $updatedData);

        $this->assertInstanceOf(Unit::class, $updatedUnit);
        $this->assertEquals($unit->id, $updatedUnit->id);
        $this->assertEquals($updatedData['name'], $updatedUnit->name);
        $this->assertEquals($updatedData['description'], $updatedUnit->description);
        $this->assertEquals($updatedData['department_id'], $updatedUnit->department_id);
    }
    public function testUpdateUnitWithValidationErrors(): void
    {
        $department = department::factory()->create();
        $nonExistentDepartmentId = mt_rand(1000000000, 9999999999);

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Test Description',
            'department_id' => $department->id,
        ]);

        $invalidData = [
            'description' => 'Updated Description',
            'department_id' => $nonExistentDepartmentId,
        ];

        $unitService = new UnitService();
        $updateFailed = $unitService->updateUnit($unit->id, $invalidData);

        $this->assertIsArray($updateFailed);
        $this->assertArrayHasKey('department_id', $updateFailed);
        $this->assertEquals(['The selected department id is invalid.'], $updateFailed['department_id']);

        $this->assertDatabaseMissing('units', [
            'id' => $unit->id,
            'name' => 'Updated Unit'
        ]);
    }
    public function testUpdateNonExistentUnit(): void
    {
        $nonExistentUnitId = mt_rand(1000000000, 9999999999);
        $request = [
            'name' => 'Updated Unit Name',
            'description' => 'Updated Unit Description',
        ];

        $unitService = new UnitService();
        $updateFailed = $unitService->updateUnit($nonExistentUnitId, $request);

        $this->assertFalse($updateFailed);
    }
    public function testGetExistingUnit(): void
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

        $retrievedUnit = $unitService->getUnit($createdUnit->id);

        // Assert that the retrieved unit is an instance of Unit
        $this->assertInstanceOf(Unit::class, $retrievedUnit);

        // Assert that the retrieved unit has the correct ID
        $this->assertEquals($createdUnit->id, $retrievedUnit->id);
    }

    public function testGetNonExistentUnit(): void
    {
        // Generate a random unit ID that doesn't exist
        $nonExistentUnitId = mt_rand(1000000000, 9999999999);

        $unitService = new UnitService();
        $retrievedUnit = $unitService->getUnit($nonExistentUnitId);

        // Assert that the retrieved unit is null for a non-existent unit
        $this->assertNull($retrievedUnit);
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
    /**
     * Test deleting an existing unit.
     */
    public function testDeleteExistingUnit(): void
    {
        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Test Description',
            'department_id' => $department->id,
        ]);

        $unitService = new UnitService();
        $deletionResult = $unitService->deleteUnit($unit->id);

        $this->assertTrue($deletionResult);

        $this->assertDatabaseMissing('units', [
            'id' => $unit->id,
        ]);
    }

    /**
     * Test deleting a non-existent unit.
     */
    public function testDeleteNonExistentUnit(): void
    {
        $nonExistentUnitId = mt_rand(1000000000, 9999999999);
        $unitService = new UnitService();
        $deletionResult = $unitService->deleteUnit($nonExistentUnitId);

        $this->assertFalse($deletionResult);
    }

      /**
     * Test viewing an existing unit.
     */
    public function testViewExistingUnit(): void
    {

        $department = department::factory()->create();

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Test Description',
            'department_id' => $department->id,
        ]);

        $unitService = new UnitService();
        $retrievedUnit = $unitService->viewUnit($unit->id);

        // Assert that the unit is retrieved successfully
        $this->assertInstanceOf(Unit::class, $retrievedUnit);
        $this->assertEquals($unit->id, $retrievedUnit->id);
    }

    /**
     * Test viewing a non-existent unit.
     */
    public function testViewNonExistentUnit(): void
    {
        $nonExistentUnitId = mt_rand(1000000000, 9999999999);
        $unitService = new UnitService();
        $retrievedUnit = $unitService->viewUnit($nonExistentUnitId);

        // Assert that the unit is not found
        $this->assertNull($retrievedUnit);
    }
}
