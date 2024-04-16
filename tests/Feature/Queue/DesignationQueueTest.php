<?php

namespace Tests\Feature\Queue;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Designation\DesignationCreated;
use App\Jobs\Designation\DesignationDeleted;
use App\Jobs\Designation\DesignationUpdated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DesignationQueueTest extends TestCase
{


    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

// TODO: uncomment designation queue tests below and remove the testcas above this comment

    // public function test_it_dispatches_designation_created_job()
    // {
    //     Queue::fake();
    //     $designation = Designation::factory()->create();
    //     DesignationCreated::dispatch($designation->toArray());
    //     Queue::assertPushed(DesignationCreated::class, function ($job) use ($designation) {
    //         return $job->getData() == $designation->toArray();
    //     });
    // }

    // public function test_it_dispatches_designation_updated_job()
    // {
    //     Queue::fake();
    //     $designation = Designation::factory()->create();

    //     DesignationUpdated::dispatch($designation->toArray());
    //     Queue::assertPushed(DesignationUpdated::class, function ($job) use ($designation) {
    //         return $job->getData() == $designation->toArray();
    //     });
    // }

    // public function test_it_dispatches_designation_deleted_job(): void
    // {
    //     Queue::fake();
    //     $designation = Designation::factory()->create();

    //     DesignationDeleted::dispatch($designation->id);
    //     Queue::assertPushed(DesignationDeleted::class, function ($job) use ($designation) {
    //         return $job->getId() == $designation->id;
    //     });
    // }
}