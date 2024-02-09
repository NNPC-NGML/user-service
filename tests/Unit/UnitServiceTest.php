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
 
}
