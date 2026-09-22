<?php

namespace Database\Seeders;

use App\Models\DeliveryCarrier;
use App\Models\MeetingRoom;
use App\Models\Organization;
use App\Models\PackageSize;
use Illuminate\Database\Seeder;

class DeliveryAndMeetingRoomSeeder extends Seeder
{
    public function run(): void
    {
        $orgId = Organization::first()?->id ?? 1;

        $carriers = ['DHL Express', 'FedEx', 'Aramex', 'Amazon Logistics', 'Emirates Post'];
        foreach ($carriers as $name) {
            DeliveryCarrier::firstOrCreate([
                'organization_id' => $orgId,
                'name' => $name,
            ]);
        }

        $sizes = ['Envelope / Document', 'Small Box', 'Medium Box', 'Large Box', 'Pallet / Freight'];
        foreach ($sizes as $name) {
            PackageSize::firstOrCreate([
                'organization_id' => $orgId,
                'name' => $name,
            ]);
        }

        $rooms = ['Boardroom Alpha', 'Conference Room 4B', 'Meeting Room 2A', 'Executive Suite C'];
        foreach ($rooms as $name) {
            MeetingRoom::firstOrCreate([
                'organization_id' => $orgId,
                'name' => $name,
            ]);
        }
    }
}
