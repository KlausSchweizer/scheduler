<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory()->count(4)->create();

        Task::factory()->count(2)->create()->each(function ($task) {
            $task->delete();
            $task->update(['deleted_at' => Carbon::now()->subDays(5)]);
        });

        Task::factory()->count(3)->create()->each(function ($task) {
            $task->update(['deleted_at' => Carbon::now()->subDays(45)]);
        });
    }
}
