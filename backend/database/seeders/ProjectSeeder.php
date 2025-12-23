<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $manager = User::where('role', 'manager')->first();

        // Create published projects with shifts
        foreach ($clients->take(5) as $client) {
            $project = Project::factory()
                ->for($client)
                ->create([
                    'created_by' => $manager->id,
                    'status' => 'published',
                ]);

            // Create shifts for each project
            $startDate = $project->start_date;
            $endDate = $project->end_date;
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                Shift::factory()
                    ->for($project)
                    ->create([
                        'date' => $currentDate->format('Y-m-d'),
                    ]);

                $currentDate->addDay();
            }
        }

        // Create draft projects
        Project::factory()
            ->count(3)
            ->draft()
            ->create([
                'client_id' => $clients->random()->id,
                'created_by' => $manager->id,
            ]);

        // Create completed projects
        $completedProjects = Project::factory()
            ->count(2)
            ->completed()
            ->create([
                'client_id' => $clients->random()->id,
                'created_by' => $manager->id,
            ]);

        foreach ($completedProjects as $project) {
            Shift::factory()
                ->count(3)
                ->for($project)
                ->create([
                    'date' => $project->start_date,
                ]);
        }
    }
}
