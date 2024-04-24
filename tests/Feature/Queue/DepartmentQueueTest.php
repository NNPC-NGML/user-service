<?php

namespace Tests\Feature\Queue;

use Tests\TestCase;
use App\Models\department;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Department\DepartmentCreated;
use App\Jobs\Department\DepartmentDeleted;
use App\Jobs\Department\DepartmentUpdated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_department_created_job()
    {
        Queue::fake();
        $department = department::factory()->create();
        DepartmentCreated::dispatch($department->toArray());
        Queue::assertPushed(DepartmentCreated::class, function ($job) use ($department) {
            return $job->getData() == $department->toArray();
        });
    }

    public function test_it_dispatches_department_updated_job()
    {
        Queue::fake();
        $department = department::factory()->create();

        DepartmentUpdated::dispatch($department->toArray());
        Queue::assertPushed(DepartmentUpdated::class, function ($job) use ($department) {
            return $job->getData() == $department->toArray();
        });
    }

    public function test_it_dispatches_department_deleted_job(): void
    {
        Queue::fake();
        $department = department::factory()->create();

        DepartmentDeleted::dispatch($department->id);
        Queue::assertPushed(DepartmentDeleted::class, function ($job) use ($department) {
            return $job->getId() == $department->id;
        });
    }
}