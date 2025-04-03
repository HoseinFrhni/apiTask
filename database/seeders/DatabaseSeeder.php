<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'name' => 'hosein',
             'email' => 'test@example.com',
         ]);

        Task::create([
            'user_id' => 1,
            'title' => 'نمونه تسک',
            'description' => 'توضیحات تستی',
            'status' => 'pending',
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-02',
        ]);

    }
}
