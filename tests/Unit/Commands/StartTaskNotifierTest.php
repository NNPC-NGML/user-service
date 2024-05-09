<?php

namespace Tests\Unit\Commands;

// use PHPUnit\Framework\TestCase;

// class StartTaskNotifierTest extends TestCase
// {
//     /**
//      * A basic unit test example.
//      */
//     public function test_example(): void
//     {
//         $this->assertTrue(true);
//     }
// }






use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class StartTaskNotifierTest extends TestCase
{
    use RefreshDatabase;

    // /**
    //  * A basic feature test example.
    //  *
    //  * @return void
    //  */
    // public function test_send_task_reminders_command_sends_reminders_for_tasks_between_8am_and_4pm_daily()
    // {
    //     // Create a task with status 'visible' and created at 10am
    //     $task = Task::factory()->create([
    //         'status' => 'visible',
    //         'created_at' => Carbon::now()->subHours(2)
    //     ]);

    //     // Create another task with status 'staled' and created at 11am
    //     $task2 = Task::factory()->create([
    //         'status' => 'staled',
    //         'created_at' => Carbon::now()->subHours(1)
    //     ]);

    //     // Create a task with status 'completed' and created at 12pm
    //     $task3 = Task::factory()->create([
    //         'status' => 'completed',
    //         'created_at' => Carbon::now()->subHours(0)
    //     ]);

    //     // Run the command
    //     Artisan::call('app:send-task-reminders');

    //     // Assert that the command sent reminders for the first two tasks
    //     $this->assertStringContainsString('Reminder for task: ' . $task->title, $this->output());
    //     $this->assertStringContainsString('Reminder for task: ' . $task2->title, $this->output());
    //     $this->assertStringNotContainsString('Reminder for task: ' . $task3->title, $this->output());
    // }

    // public function test_send_task_reminders_command_does_not_send_reminders_for_tasks_outside_8am_and_4pm()
    // {
    //     // Create a task with status 'visible' and created at 5pm
    //     $task = Task::factory()->create([
    //         'status' => 'visible',
    //         'created_at' => Carbon::now()->addHours(3)
    //     ]);

    //     // Run the command
    //     Artisan::call('app:send-task-reminders');

    //     // Assert that the command did not send a reminder for the task
    //     $this->assertStringNotContainsString('Reminder for task: ' . $task->title, $this->output());
    // }

    // public function test_send_task_reminders_command_sends_reminders_for_tasks_with_status_visible_or_staled()
    // {
    //     // Create a task with status 'completed' and created at 10am
    //     $task = Task::factory()->create([
    //         'status' => 'completed',
    //         'created_at' => Carbon::now()->subHours(2)
    //     ]);

    //     // Run the command
    //     Artisan::call('app:send-task-reminders');

    //     // Assert that the command did not send a reminder for the task
    //     $this->assertStringNotContainsString('Reminder for task: ' . $task->title, $this->output());
    // }

    // public function test_send_task_reminders_command_sends_reminders_for_tasks_created_today_between_8am_and_4pm()
    // {
    //     // Create a task with status 'visible' and created yesterday at 10am
    //     $task = Task::factory()->create([
    //         'status' => 'visible',
    //         'created_at' => Carbon::now()->subDays(1)->addHours(2)
    //     ]);

    //     // Run the command
    //     Artisan::call('app:send-task-reminders');

    //     // Assert that the command did not send a reminder for the task
    //     $this->assertStringNotContainsString('Reminder for task: ' . $task->title, $this->output());
    // }

    // public function test_send_task_reminders_command_sends_reminders_for_tasks_with_status_visible_or_staled_and_created_today_between_8am_and_4pm()
    // {
    //     // Create a task with status 'completed' and created today at 10am
    //     $task = Task::factory()->create([
    //         'status' => 'completed',
    //         'created_at' => Carbon::now()->subHours(2)
    //     ]);

    //     // Run the command
    //     Artisan::call('app:send-task-reminders');

    //     // Assert that the command did not send a reminder for the task
    //     $this->assertStringNotContainsString('Reminder for task: ' . $task->title, $this->output());
    // }
}