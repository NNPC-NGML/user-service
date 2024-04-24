<?php

namespace Tests\Feature\Queue;

use Tests\TestCase;
use App\Models\Location;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Location\LocationCreated;
use App\Jobs\Location\LocationDeleted;
use App\Jobs\Location\LocationUpdated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_location_created_job()
    {
        Queue::fake();
        $location = Location::factory()->create();
        LocationCreated::dispatch($location->toArray());
        Queue::assertPushed(LocationCreated::class, function ($job) use ($location) {
            return $job->getData() == $location->toArray();
        });
    }

    public function test_it_dispatches_location_updated_job()
    {
        Queue::fake();
        $location = Location::factory()->create();

        LocationUpdated::dispatch($location->toArray());
        Queue::assertPushed(LocationUpdated::class, function ($job) use ($location) {
            return $job->getData() == $location->toArray();
        });
    }

    public function test_it_dispatches_location_deleted_job(): void
    {
        Queue::fake();
        $location = Location::factory()->create();

        LocationDeleted::dispatch($location->id);
        Queue::assertPushed(LocationDeleted::class, function ($job) use ($location) {
            return $job->getId() == $location->id;
        });
    }
}