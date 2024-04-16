<?php

namespace App\Jobs\Designation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DesignationDeleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private int $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle(): void
    {

    }
    public function getId(): int
    {
        return $this->id;
    }
}