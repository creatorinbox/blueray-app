<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Create sample company
        $company = Company::updateOrCreate(
            ['vat_trn' => 'OM1234567890'],
            [
                'company_name' => 'BluRay National Software',
                'base_currency' => 'OMR',
                'vat_rate' => 5.00,
                'financial_year_start' => '2025-01-01',
                'financial_year_end' => '2025-12-31',
                'is_active' => true,
            ]
        );

        // Create admin user
        $adminRole = Role::where('name', 'admin')->first();
        
        User::updateOrCreate(
            ['email' => 'admin@bluray.com'],
            [
                'company_id' => $company->id,
                'name' => 'System Administrator',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'status' => true,
                'role_id' => $adminRole?->id,
            ]
        );

        // Create sample users for different roles
        $sampleUsers = [
            ['name' => 'Purchase Executive', 'email' => 'purchase@bluray.com', 'role' => 'purchase_executive'],
            ['name' => 'Purchase Manager', 'email' => 'purchase.manager@bluray.com', 'role' => 'purchase_manager'],
            ['name' => 'Sales Executive', 'email' => 'sales@bluray.com', 'role' => 'sales_executive'],
            ['name' => 'Sales Manager', 'email' => 'sales.manager@bluray.com', 'role' => 'sales_manager'],
            ['name' => 'Finance Manager', 'email' => 'finance@bluray.com', 'role' => 'finance_manager'],
        ];

        foreach ($sampleUsers as $userData) {
            $role = Role::where('name', $userData['role'])->first();
            
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'company_id' => $company->id,
                    'name' => $userData['name'],
                    'role' => $userData['role'],
                    'password' => Hash::make('password123'),
                    'status' => true,
                    'role_id' => $role?->id,
                ]
            );
        }
    }
}
