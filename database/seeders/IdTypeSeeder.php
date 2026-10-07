<?php

namespace Database\Seeders;

use App\Models\IdType;
use Illuminate\Database\Seeder;

class IdTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Emirates ID', 'code' => 'emirates_id', 'status' => 'active'],
            ['name' => 'Passport', 'code' => 'passport', 'status' => 'active'],
            ['name' => 'GCC National ID', 'code' => 'gcc_id', 'status' => 'active'],
            ['name' => 'Diplomatic ID', 'code' => 'diplomatic_id', 'status' => 'inactive'],
        ];

        foreach ($types as $t) {
            IdType::updateOrCreate(['code' => $t['code']], $t);
        }
    }
}
