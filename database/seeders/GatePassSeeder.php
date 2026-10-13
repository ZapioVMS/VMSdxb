<?php

namespace Database\Seeders;

use App\Models\GatePass;
use App\Models\Organization;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GatePassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organization::first();
        $orgId = $org?->id ?? 1;

        $site = Site::where('name', 'LIKE', '%Business Bay%')->first() ?? Site::first();
        $siteId = $site?->id;

        $samplePasses = [
            [
                'pass_code' => 'GP-84721',
                'type' => 'visitor',
                'visitor_name' => 'Thomas Becker',
                'company_plate' => 'Becker Technologies',
                'purpose' => 'Business Meeting',
                'id_type' => 'Emirates ID',
                'id_number' => '784-1988-1234567-1',
                'email' => 't.becker@beckertech.ae',
                'phone' => '+971 50 123 4567',
                'expected_date' => Carbon::today(),
                'expected_duration' => '1 hour',
                'office_unit' => 'Office 202 - Becker Suite',
                'notes' => 'Attending quarterly review meeting.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84722',
                'type' => 'visitor',
                'visitor_name' => 'Noor Al-Mansoor',
                'company_plate' => 'Al-Mansoor Consulting',
                'purpose' => 'Client Visit',
                'id_type' => 'Emirates ID',
                'id_number' => '784-1992-8765432-1',
                'email' => 'noor@almansoor.ae',
                'phone' => '+971 55 987 6543',
                'expected_date' => Carbon::today(),
                'expected_duration' => '2 hours',
                'office_unit' => 'Office 405 - Legal Wing',
                'notes' => 'Consulting discussion with executive partners.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84723',
                'type' => 'delivery',
                'carrier' => 'FedEx',
                'package_size' => 'Medium Box',
                'tracking_number' => 'FX-88391024',
                'description' => 'Server Components & Peripherals',
                'office_unit' => 'Office 101 - IT Server Room',
                'notes' => 'Fragile server equipment delivery.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84724',
                'type' => 'visitor',
                'visitor_name' => 'Layla Mahmoud',
                'company_plate' => 'Continental Media Group',
                'purpose' => 'Business Meeting',
                'id_type' => 'Emirates ID',
                'id_number' => '784-1995-3456789-2',
                'email' => 'layla.m@continentalmedia.com',
                'phone' => '+971 52 334 5566',
                'expected_date' => Carbon::today(),
                'expected_duration' => '1 hour',
                'office_unit' => 'Office 301 - Media Studio',
                'notes' => 'Contract signature and branding review.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84725',
                'type' => 'delivery',
                'carrier' => 'DHL',
                'package_size' => 'Envelope',
                'tracking_number' => 'DHL-99214481',
                'description' => 'Signed Corporate Agreements',
                'office_unit' => 'Office 502 - Financial Services',
                'notes' => 'Confidential document envelope.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84726',
                'type' => 'visitor',
                'visitor_name' => 'Faisal Al-Zaabi',
                'company_plate' => 'Zaabi Group',
                'purpose' => 'Maintenance / Contractor',
                'id_type' => 'UAE Driving License',
                'id_number' => 'DXB-984210',
                'email' => 'faisal@zaabigroup.ae',
                'phone' => '+971 50 882 1934',
                'expected_date' => Carbon::today(),
                'expected_duration' => 'Half day (4h)',
                'office_unit' => 'Floor 6 - Facilities Engineering',
                'notes' => 'HVAC quarterly scheduled inspection.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84727',
                'type' => 'delivery',
                'carrier' => 'Aramex',
                'package_size' => 'Small Box',
                'tracking_number' => 'ARX-33910294',
                'description' => 'Marketing Brochures & Banners',
                'office_unit' => 'Office 204 - Marketing Hub',
                'notes' => 'Delivered to reception desk.',
                'status' => 'active',
            ],
            [
                'pass_code' => 'GP-84728',
                'type' => 'visitor',
                'visitor_name' => 'Sarah Jenkins',
                'company_plate' => 'Apex Financial UK',
                'purpose' => 'Interview',
                'id_type' => 'Passport',
                'id_number' => 'GB9821034',
                'email' => 's.jenkins@apexfinancial.co.uk',
                'phone' => '+971 56 441 9021',
                'expected_date' => Carbon::today(),
                'expected_duration' => '1 hour',
                'office_unit' => 'Office 402 - HR Conference Room',
                'notes' => 'Senior Portfolio Director candidate interview.',
                'status' => 'active',
            ],
        ];

        foreach ($samplePasses as $passData) {
            GatePass::updateOrCreate(
                ['pass_code' => $passData['pass_code']],
                array_merge($passData, [
                    'organization_id' => $orgId,
                    'site_id' => $siteId,
                ])
            );
        }
    }
}
