<?php

namespace Tests\Feature\Queue;

use App\Jobs\Unit\UnitDeleted;
use Tests\TestCase;
use App\Models\Unit;
use App\Service\UnitService;
use App\Jobs\Unit\UnitCreated;
use App\Jobs\Unit\UnitUpdated;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UnitQueueTest extends TestCase
{


    use RefreshDatabase;

    public function test_it_dispatches_unit_created_job()
    {
        Queue::fake();
        $unit = Unit::factory()->create();
        UnitCreated::dispatch($unit->toArray());
        Queue::assertPushed(UnitCreated::class, function ($job) use ($unit) {
            return $job->getData() == $unit->toArray();
        });
    }

    public function test_it_dispatches_unit_updated_job()
    {
        Queue::fake();
        $unit = Unit::factory()->create();

        UnitUpdated::dispatch($unit->toArray());
        Queue::assertPushed(UnitUpdated::class, function ($job) use ($unit) {
            return $job->getData() == $unit->toArray();
        });
    }

    public function test_it_dispatches_unit_deleted_job(): void
    {
        Queue::fake();
        $unit = Unit::factory()->create();

        UnitDeleted::dispatch($unit->id);
        Queue::assertPushed(UnitDeleted::class, function ($job) use ($unit) {
            return $job->getId() == $unit->id;
        });
    }
}