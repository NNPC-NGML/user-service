<?php

namespace Tests\Feature\Queue;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\User\UserCreated;
use App\Jobs\User\UserDeleted;
use App\Jobs\User\UserUpdated;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserQueueTest extends TestCase
{

    use RefreshDatabase;
    public function test_it_dispatches_user_created_job()
    {
        Queue::fake();
        $user = User::factory()->create();
        UserCreated::dispatch($user->toArray());
        Queue::assertPushed(UserCreated::class, function ($job) use ($user) {
            return $job->getData() == $user->toArray();
        });
    }

    public function test_it_dispatches_user_updated_job()
    {
        Queue::fake();
        $user = User::factory()->create();

        UserUpdated::dispatch($user->toArray());
        Queue::assertPushed(UserUpdated::class, function ($job) use ($user) {
            return $job->getData() == $user->toArray();
        });
    }

    public function test_it_dispatches_user_deleted_job(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        UserDeleted::dispatch($user->id);
        Queue::assertPushed(UserDeleted::class, function ($job) use ($user) {
            return $job->getId() == $user->id;
        });
    }
}