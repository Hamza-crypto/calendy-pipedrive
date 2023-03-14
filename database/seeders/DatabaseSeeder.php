<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $stages = [
            [
                'stage_id' => 6,
                'name' => 'Lead In',
                'sms' => 'Hello from Pipedrive for Lead In',
                'voice' => 'Hello from Pipedrive'
            ],
            [
                'stage_id' => 7,
                'name' => 'Contact Made',
                'sms' => 'Hello from Pipedrive for Contact Made',
                'voice' => 'Hello from Pipedrive'
            ],
            [
                'stage_id' => 8,
                'name' => 'Property Evaluated',
                'sms' => null,
                'voice' => 'Hello from Pipedrive'
            ],
        ];

        foreach ($stages as $stage) {
            \App\Models\Stage::create($stage);
        }
    }
}
