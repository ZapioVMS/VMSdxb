<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'United Arab Emirates', 'iso_code' => 'AE', 'isd_code' => '971'],
            ['name' => 'Saudi Arabia', 'iso_code' => 'SA', 'isd_code' => '966'],
            ['name' => 'Qatar', 'iso_code' => 'QA', 'isd_code' => '974'],
            ['name' => 'Kuwait', 'iso_code' => 'KW', 'isd_code' => '965'],
            ['name' => 'Bahrain', 'iso_code' => 'BH', 'isd_code' => '973'],
            ['name' => 'Oman', 'iso_code' => 'OM', 'isd_code' => '968'],
            ['name' => 'Jordan', 'iso_code' => 'JO', 'isd_code' => '962'],
            ['name' => 'Lebanon', 'iso_code' => 'LB', 'isd_code' => '961'],
            ['name' => 'Egypt', 'iso_code' => 'EG', 'isd_code' => '20'],
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'isd_code' => '44'],
            ['name' => 'United States', 'iso_code' => 'US', 'isd_code' => '1'],
            ['name' => 'India', 'iso_code' => 'IN', 'isd_code' => '91'],
            ['name' => 'Pakistan', 'iso_code' => 'PK', 'isd_code' => '92'],
            ['name' => 'Germany', 'iso_code' => 'DE', 'isd_code' => '49'],
            ['name' => 'France', 'iso_code' => 'FR', 'isd_code' => '33'],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(['iso_code' => $c['iso_code']], $c);
        }
    }
}
