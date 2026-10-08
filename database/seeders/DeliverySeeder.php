<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $orgId = Organization::first()?->id ?? 1;
        $site = Site::first();
        $employees = Employee::with('department')->get();

        if ($employees->isEmpty()) {
            return;
        }

        // Only seed if no deliveries exist
        if (Delivery::count() > 0) {
            return;
        }

        $emp1 = $employees->first();
        $emp2 = $employees->skip(1)->first() ?? $emp1;
        $emp3 = $employees->skip(2)->first() ?? $emp1;

        $items = [
            [
                'carrier' => 'Emirates Post',
                'tracking_number' => 'EMI-677581',
                'size' => 'Small Box',
                'description' => 'Small parcel',
                'recipient_employee_id' => $emp1->id,
                'recipient_name' => $emp1->first_name . ' ' . $emp1->last_name,
                'recipient_email' => $emp1->email,
                'department' => $emp1->department?->name ?? 'Operations',
                'received_at' => Carbon::now()->subDays(10)->setTime(13, 40),
                'received_by' => 'Front Desk',
                'status' => 'awaiting-pickup',
            ],
            [
                'carrier' => 'DHL',
                'tracking_number' => 'DHL-293731',
                'size' => 'Envelope / Document',
                'description' => 'Signed envelope',
                'recipient_employee_id' => $emp2->id,
                'recipient_name' => $emp2->first_name . ' ' . $emp2->last_name,
                'recipient_email' => $emp2->email,
                'department' => $emp2->department?->name ?? 'Operations',
                'received_at' => Carbon::now()->subDays(10)->setTime(11, 00),
                'received_by' => 'Front Desk',
                'status' => 'collected',
                'collected_at' => Carbon::now()->subDays(10)->setTime(13, 00),
                'collected_by' => $emp2->first_name . ' ' . $emp2->last_name,
            ],
            [
                'carrier' => 'Aramex',
                'tracking_number' => 'ARA-758461',
                'size' => 'Small Box',
                'description' => 'Small box',
                'recipient_employee_id' => $emp3->id,
                'recipient_name' => $emp3->first_name . ' ' . $emp3->last_name,
                'recipient_email' => $emp3->email,
                'department' => $emp3->department?->name ?? 'Executive Office',
                'received_at' => Carbon::now()->subDays(10)->setTime(9, 15),
                'received_by' => 'Front Desk',
                'status' => 'awaiting-pickup',
            ],
            [
                'carrier' => 'UPS',
                'tracking_number' => 'UPS-645917',
                'size' => 'Medium Box',
                'description' => 'Medium box - fragile',
                'recipient_employee_id' => $emp3->id,
                'recipient_name' => $emp3->first_name . ' ' . $emp3->last_name,
                'recipient_email' => $emp3->email,
                'department' => $emp3->department?->name ?? 'Executive Office',
                'received_at' => Carbon::now()->subDays(11)->setTime(15, 00),
                'received_by' => 'Front Desk',
                'status' => 'collected',
                'collected_at' => Carbon::now()->subDays(11)->setTime(17, 30),
                'collected_by' => $emp3->first_name . ' ' . $emp3->last_name,
            ],
            [
                'carrier' => 'FedEx',
                'tracking_number' => 'FDX-882103',
                'size' => 'Medium Box',
                'description' => 'Medium box',
                'recipient_employee_id' => $emp1->id,
                'recipient_name' => $emp1->first_name . ' ' . $emp1->last_name,
                'recipient_email' => $emp1->email,
                'department' => $emp1->department?->name ?? 'Operations',
                'received_at' => Carbon::now()->subDays(11)->setTime(10, 20),
                'received_by' => 'Front Desk',
                'status' => 'collected',
                'collected_at' => Carbon::now()->subDays(11)->setTime(14, 00),
                'collected_by' => $emp1->first_name . ' ' . $emp1->last_name,
            ],
            [
                'carrier' => 'DHL',
                'tracking_number' => 'DHL-554190',
                'size' => 'Pallet / Freight',
                'description' => 'Pallet of office supplies',
                'recipient_employee_id' => $emp2->id,
                'recipient_name' => $emp2->first_name . ' ' . $emp2->last_name,
                'recipient_email' => $emp2->email,
                'department' => $emp2->department?->name ?? 'IT & Security',
                'received_at' => Carbon::now()->subDays(5)->setTime(9, 45),
                'received_by' => 'Front Desk',
                'status' => 'awaiting-pickup',
            ],
            [
                'carrier' => 'Aramex',
                'tracking_number' => 'ARA-992144',
                'size' => 'Large Box',
                'description' => 'Large box',
                'recipient_employee_id' => $emp1->id,
                'recipient_name' => $emp1->first_name . ' ' . $emp1->last_name,
                'recipient_email' => $emp1->email,
                'department' => $emp1->department?->name ?? 'Operations',
                'received_at' => Carbon::now()->subDays(4)->setTime(10, 00),
                'received_by' => 'Front Desk',
                'status' => 'awaiting-pickup',
            ],
            [
                'carrier' => 'Amazon Logistics',
                'tracking_number' => 'AMZ-102938',
                'size' => 'Small Box',
                'description' => 'Headset and adapter',
                'recipient_employee_id' => $emp2->id,
                'recipient_name' => $emp2->first_name . ' ' . $emp2->last_name,
                'recipient_email' => $emp2->email,
                'department' => $emp2->department?->name ?? 'IT & Security',
                'received_at' => Carbon::now()->subDays(6)->setTime(13, 00),
                'received_by' => 'Front Desk',
                'status' => 'awaiting-pickup',
            ],
            [
                'carrier' => 'DHL Express',
                'tracking_number' => 'DHL-771239',
                'size' => 'Envelope / Document',
                'description' => 'Contract documents',
                'recipient_employee_id' => $emp3->id,
                'recipient_name' => $emp3->first_name . ' ' . $emp3->last_name,
                'recipient_email' => $emp3->email,
                'department' => $emp3->department?->name ?? 'Executive Office',
                'received_at' => Carbon::now()->subDays(12)->setTime(14, 00),
                'received_by' => 'Front Desk',
                'status' => 'collected',
                'collected_at' => Carbon::now()->subDays(12)->setTime(16, 00),
                'collected_by' => $emp3->first_name . ' ' . $emp3->last_name,
            ],
        ];

        $counter = 1;
        foreach ($items as $item) {
            Delivery::create(array_merge($item, [
                'organization_id' => $orgId,
                'site_id' => $site?->id,
                'delivery_code' => 'DLV' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
                'notified_at' => $item['received_at'],
                'notify_recipient' => true,
            ]));
        }
    }
}
