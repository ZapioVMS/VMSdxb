<?php

namespace Database\Seeders;

use App\Models\DeliveryCarrier;
use App\Models\MeetingRoom;
use App\Models\Organization;
use App\Models\PackageSize;
use App\Models\Site;
use Illuminate\Database\Seeder;

class DeliveryAndMeetingRoomSeeder extends Seeder
{
    public function run(): void
    {
        $orgId = Organization::first()?->id ?? 1;

        $sites = Site::all()->keyBy('code');
        $bbt = $sites->get('BBT') ?? Site::first();
        $dgv = $sites->get('DGV') ?? Site::skip(1)->first() ?? $bbt;

        $carriers = [
            ['name' => 'DHL Express', 'code' => 'DHL'],
            ['name' => 'FedEx', 'code' => 'FDX'],
            ['name' => 'Aramex', 'code' => 'ARMX'],
            ['name' => 'Amazon Logistics', 'code' => 'AMZN'],
            ['name' => 'Emirates Post', 'code' => 'EMPOST'],
        ];
        foreach ($carriers as $c) {
            DeliveryCarrier::updateOrCreate(
                ['organization_id' => $orgId, 'name' => $c['name']],
                ['code' => $c['code']]
            );
        }

        $sizes = [
            ['name' => 'Envelope / Document', 'description' => 'Flat documents, legal papers & letters'],
            ['name' => 'Small Box', 'description' => 'Up to 30x20x15 cm (fits in small locker)'],
            ['name' => 'Medium Box', 'description' => 'Up to 50x40x30 cm (standard shipping carton)'],
            ['name' => 'Large Box', 'description' => 'Over 50 cm or weighing > 10 kg'],
            ['name' => 'Pallet / Freight', 'description' => 'Bulk equipment, requires loading bay access'],
        ];
        foreach ($sizes as $s) {
            PackageSize::updateOrCreate(
                ['organization_id' => $orgId, 'name' => $s['name']],
                ['description' => $s['description']]
            );
        }

        $rooms = [
            ['name' => 'Boardroom Alpha', 'capacity' => 20, 'site_id' => $bbt?->id],
            ['name' => 'Conference Room 4B', 'capacity' => 10, 'site_id' => $bbt?->id],
            ['name' => 'Meeting Room 2A', 'capacity' => 6, 'site_id' => $bbt?->id],
            ['name' => 'Executive Suite C', 'capacity' => 8, 'site_id' => $dgv?->id],
        ];
        foreach ($rooms as $r) {
            MeetingRoom::updateOrCreate(
                ['organization_id' => $orgId, 'name' => $r['name']],
                ['capacity' => $r['capacity'], 'site_id' => $r['site_id']]
            );
        }
    }
}
