<?php

namespace App\Jobs\DepartmentAssignment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DepartmentAssignmentCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public array $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function handle(): void
    {
        //
    }

    public function getData(): array
    {
        return $this->data;
    }
}
