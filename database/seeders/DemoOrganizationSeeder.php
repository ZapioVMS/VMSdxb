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

        // 7. Create Users
        $user1 = User::updateOrCreate(
            ['email' => 'allanjacob03@gmail.com'],
            [
                'organization_id' => $org->id,
                'employee_id' => $emp1->id,
                'name' => 'Allan Jacob',
                'password' => Hash::make('password'),
                'status' => 'active',
                'two_factor_enabled' => true,
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'sarah.khan@zapio.ae'],
            [
                'organization_id' => $org->id,
                'employee_id' => $emp2->id,
                'name' => 'Sarah Khan',
                'password' => Hash::make('password'),
                'status' => 'active',
                'two_factor_enabled' => true,
            ]
        );

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
