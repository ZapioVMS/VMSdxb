<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Seeder;

class FloorAndAreaSeeder extends Seeder
{
    public function run(): void
    {
        $orgId = Organization::first()?->id ?? 1;

        $sites = Site::all()->keyBy('code');
        if ($sites->isEmpty()) {
            return;
        }

        $bbt = $sites->get('BBT') ?? Site::first();
        $dgv = $sites->get('DGV') ?? Site::skip(1)->first() ?? $bbt;

        $seedData = [
            // Business Bay Tower
            [
                'site' => $bbt,
                'name' => 'Ground Floor',
                'description' => 'Reception, lobby, main entrance',
                'areas' => ['Reception Desk', 'Lobby', 'Security'],
            ],
            [
                'site' => $bbt,
                'name' => 'Floor 1',
                'description' => 'IT, server room, utility',
                'areas' => ['IT & Infrastructure', 'Server Room', 'Training Room'],
            ],
            [
                'site' => $bbt,
                'name' => 'Floor 2',
                'description' => 'Engineering & development',
                'areas' => ['Engineering', 'QA Lab', 'Meeting Room A'],
            ],
            // DIFC Gate Village
            [
                'site' => $dgv,
                'name' => 'Ground Floor',
                'description' => 'Reception and lobby',
                'areas' => ['Reception Desk', 'Lobby'],
            ],
            [
                'site' => $dgv,
                'name' => 'Floor 3',
                'description' => 'Operations & HR',
                'areas' => ['Operations', 'Human Resources', 'Meeting Room B'],
            ],
            [
                'site' => $dgv,
                'name' => 'Floor 4',
                'description' => 'Finance & marketing',
                'areas' => ['Finance', 'Marketing', 'Conference Room'],
            ],
        ];

        foreach ($seedData as $item) {
            $site = $item['site'];
            if (!$site) {
                continue;
            }

            $building = Building::firstOrCreate(
                ['organization_id' => $orgId, 'site_id' => $site->id],
                ['name' => $site->name . ' Tower', 'status' => 'active']
            );

            $floor = Floor::updateOrCreate(
                [
                    'organization_id' => $orgId,
                    'site_id' => $site->id,
                    'name' => $item['name'],
                ],
                [
                    'building_id' => $building->id,
                    'description' => $item['description'],
                    'status' => 'active',
                ]
            );

            foreach ($item['areas'] as $areaName) {
                Area::firstOrCreate(
                    [
                        'organization_id' => $orgId,
                        'floor_id' => $floor->id,
                        'name' => $areaName,
                    ],
                    [
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
