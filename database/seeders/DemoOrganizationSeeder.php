<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Site;
use App\Models\User;
use App\Models\VisitorType;
use App\Models\VisitPurpose;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Primary Organization
        $org = Organization::updateOrCreate(
            ['name' => 'Meridian Business Solutions LLC'],
            [
                'trade_license_no' => 'CN-1487293',
                'timezone' => 'Asia/Dubai',
                'default_language' => 'en',
                'status' => 'active',
            ]
        );

        // 2. Create Sites
        $bbt = Site::updateOrCreate(
            ['organization_id' => $org->id, 'code' => 'BBT'],
            [
                'name' => 'Business Bay Tower',
                'name_ar' => 'برج الخليج التجاري',
                'city' => 'Dubai',
                'address' => 'Business Bay, Dubai, UAE',
                'timezone' => 'Asia/Dubai',
                'capacity' => 1200,
                'status' => 'active',
            ]
        );

        $dgv = Site::updateOrCreate(
            ['organization_id' => $org->id, 'code' => 'DGV'],
            [
                'name' => 'DIFC Gate Village',
                'name_ar' => 'قرية البوابة',
                'city' => 'Dubai',
                'address' => 'DIFC, Dubai, UAE',
                'timezone' => 'Asia/Dubai',
                'capacity' => 600,
                'status' => 'active',
            ]
        );

        // 3. Create Departments
        $depts = [
            ['name' => 'Facilities', 'code' => 'FAC', 'description' => 'Facility management & logistics'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Recruitment & employee relations'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Daily operations oversight'],
            ['name' => 'IT & Security', 'code' => 'ITSEC', 'description' => 'Infrastructure & security'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Budgeting & financial reporting'],
            ['name' => 'Legal & Compliance', 'code' => 'LEGAL', 'description' => 'Contracts & compliance'],
            ['name' => 'Sales', 'code' => 'SALES', 'description' => 'Client relationships & sales'],
            ['name' => 'Executive Office', 'code' => 'EXEC', 'description' => 'Company leadership'],
        ];

        $deptModels = [];
        foreach ($depts as $d) {
            $deptModels[$d['name']] = Department::updateOrCreate(
                ['organization_id' => $org->id, 'code' => $d['code']],
                array_merge($d, ['organization_id' => $org->id])
            );
        }

        // 4. Create Shifts
        $shift = Shift::updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Sun-Thu (8AM-5PM)'],
            [
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'grace_minutes' => 15,
                'status' => 'active',
            ]
        );

        // 5. Create Staff Employees
        $emp1 = Employee::updateOrCreate(
            ['organization_id' => $org->id, 'employee_code' => 'EMP-001'],
            [
                'site_id' => $bbt->id,
                'department_id' => $deptModels['IT & Security']->id,
                'first_name' => 'Allan',
                'last_name' => 'Jacob',
                'email' => 'allanjacob03@gmail.com',
                'phone' => '+971501234567',
                'job_title' => 'System Administrator',
                'shift_id' => $shift->id,
                'status' => 'active',
            ]
        );

        $emp2 = Employee::updateOrCreate(
            ['organization_id' => $org->id, 'employee_code' => 'EMP-002'],
            [
                'site_id' => $bbt->id,
                'department_id' => $deptModels['Human Resources']->id,
                'first_name' => 'Sarah',
                'last_name' => 'Khan',
                'email' => 'sarah.khan@zapio.ae',
                'phone' => '+971509876543',
                'job_title' => 'HR Manager',
                'shift_id' => $shift->id,
                'status' => 'active',
            ]
        );

        // 6. Create Roles
        $superAdminRole = Role::updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Super Admin'],
            ['description' => 'Full system access', 'status' => 'active']
        );

        $adminRole = Role::updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Admin'],
            ['description' => 'Location admin access', 'status' => 'active']
        );

 
        // 7. Create the initial organization administrator.
        $email = $emp1->email;
        $user1 = User::where('email', $email)->first();

        if ($user1 && (int) $user1->organization_id !== (int) $org->id) {
            throw new \RuntimeException(
                'This administrator email belongs to another organization.'
            );
        }

        $password = null;
        if (! $user1) {
            $password = $this->command->secret(
                'Enter a new administrator password (minimum 16 characters)'
            );

            $confirmation = $this->command->secret(
                'Confirm administrator password'
            );

            if (! is_string($password)
                || strlen($password) < 16
                || $password !== $confirmation) {
                throw new \RuntimeException(
                    'Passwords must match and contain at least 16 characters.'
                );
            }
        }

        DB::transaction(function () use (
            &$user1, $org, $emp1, $superAdminRole, $password
        ) {
            if (! $user1) {
                $user1 = User::create([
                    'organization_id' => $org->id,
                    'employee_id' => $emp1->id,
                    'name' => $emp1->first_name . ' ' . $emp1->last_name,
                    'email' => $emp1->email,
                    'password' => Hash::make($password),
                    'status' => 'active',
                    'two_factor_enabled' => false,
                ]);
            }

            DB::table('user_roles')->insertOrIgnore([
                'organization_id' => $org->id,
                'user_id' => $user1->id,
                'role_id' => $superAdminRole->id,
                'site_id' => null,
            ]);

            foreach (DB::table('permissions')->pluck('id') as $permissionId) {
                DB::table('role_permissions')->insertOrIgnore([
                    'organization_id' => $org->id,
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $permissionId,
                ]);
            }
        });


        // 8. Create Visitor Types & Visit Purposes
        $vTypes = ['Regular Visitor', 'VIP', 'Contractor', 'Interview Candidate', 'Delivery / Courier'];
        foreach ($vTypes as $vt) {
            VisitorType::updateOrCreate(
                ['organization_id' => $org->id, 'name' => $vt],
                ['requires_approval' => ($vt === 'VIP' || $vt === 'Contractor'), 'requires_nda' => ($vt === 'Contractor')]
            );
        }

        $purposes = ['Business Meeting', 'Job Interview', 'Delivery / Courier', 'Maintenance & Repairs', 'Government Inspection', 'Contractor Work'];
        foreach ($purposes as $p) {
            VisitPurpose::updateOrCreate(
                ['organization_id' => $org->id, 'name' => $p],
                ['status' => 'active']
            );
        }
    }
}
