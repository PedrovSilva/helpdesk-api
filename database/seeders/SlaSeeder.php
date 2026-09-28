<?php

namespace Database\Seeders;

use App\Enums\Priority;
use App\Models\Sla;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SlaSeeder extends Seeder
{
    public function run(): void
    {
        $slas = [
            [
                'name' => 'SLA Baixa Prioridade',
                'priority' => Priority::LOW,
                'response_time_minutes' => 480,
                'resolution_time_minutes' => 2880,
            ],
            [
                'name' => 'SLA Média Prioridade',
                'priority' => Priority::MEDIUM,
                'response_time_minutes' => 240,
                'resolution_time_minutes' => 1440,
            ],
            [
                'name' => 'SLA Alta Prioridade',
                'priority' => Priority::HIGH,
                'response_time_minutes' => 60,
                'resolution_time_minutes' => 480,
            ],
            [
                'name' => 'SLA Crítica',
                'priority' => Priority::CRITICAL,
                'response_time_minutes' => 15,
                'resolution_time_minutes' => 120,
            ],
        ];

        foreach ($slas as $sla) {
            Sla::create([
                'uuid' => (string) Str::uuid(),
                ...$sla,
                'active' => true,
            ]);
        }
    }
}
