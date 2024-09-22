<?

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Models\HeadOfUnit;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Service\HeadOfUnitService;

class HeadOfUnitServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_create_head_of_unit_successfully()
    {
        // Mock the request data
        $request = new Request([
            'user_id' => 1,
            'unit_id' => 1,
            'location_id' => 1,
            'status' => 1,
        ]);

        // Create an instance of the service
        $service = new HeadOfUnitService();
        $result = $service->create($request);

        // Assert the result is a HeadOfUnit instance
        $this->assertInstanceOf(HeadOfUnit::class, $result);
    }

    /** @test */
    public function it_should_return_validation_errors_if_validation_fails()
    {
        // Mock invalid request data
        $request = new Request([
            'unit_id' => 1,
            'location_id' => 1,
            'status' => 1,
        ]);

        // Create an instance of the service
        $service = new HeadOfUnitService();
        $result = $service->create($request);

        // Assert the result is a MessageBag instance
        $this->assertInstanceOf(\Illuminate\Support\MessageBag::class, $result);
        $this->assertTrue($result->has('user_id')); // Assert that 'user_id' validation error exists
    }

    /** @test */
    public function it_should_get_head_of_unit_by_id_successfully()
    {
        // Mock an existing HeadOfUnit
        $headOfUnit = HeadOfUnit::factory()->create();

        // Create an instance of the service
        $service = new HeadOfUnitService();
        $result = $service->getHeadOfUnitById($headOfUnit->id);

        // Assert the result is the expected HeadOfUnit
        $this->assertInstanceOf(HeadOfUnit::class, $result);
        $this->assertEquals($headOfUnit->id, $result->id);
    }

    /** @test */
    public function it_should_return_null_if_head_of_unit_not_found_by_id()
    {
        // Create an instance of the service
        $service = new HeadOfUnitService();

        // Expect a ModelNotFoundException for a non-existent ID
        $headOfUnit = $service->getHeadOfUnitById(999);
        $this->assertFalse($headOfUnit);
    }

    /** @test */
    public function it_should_get_head_of_unit_by_unit_and_location_successfully()
    {
        // Mock an existing HeadOfUnit
        $headOfUnit = HeadOfUnit::factory()->create([
            'unit_id' => 1,
            'location_id' => 1,
        ]);

        // Create an instance of the service
        $service = new HeadOfUnitService();
        $result = $service->getHeadOfUnitByUnitAndLocaltion(1, 1);

        // Assert the result is a collection and contains the expected HeadOfUnit
        $this->assertInstanceOf(HeadOfUnit::class, $result);
        $this->assertEquals($headOfUnit->id, $result->id);
    }

    /** @test */
    public function it_should_view_all_head_of_units_successfully()
    {
        // Create some HeadOfUnit records
        HeadOfUnit::factory()->count(3)->create();

        // Create an instance of the service
        $service = new HeadOfUnitService();
        $result = $service->viewAllHeadOfUnits();

        // Assert the result is a collection and contains 3 items
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }
}
