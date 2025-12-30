<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System configuration, role setup',
                'permissions' => ['*'], // All permissions
            ],
            [
                'name' => 'purchase_executive',
                'display_name' => 'Purchase Executive',
                'description' => 'Create PO, GRN',
                'permissions' => ['purchase.create', 'purchase.edit', 'grn.create'],
            ],
            [
                'name' => 'purchase_manager',
                'display_name' => 'Purchase Manager', 
                'description' => 'Approve PO, GRN',
                'permissions' => ['purchase.approve', 'grn.approve'],
            ],
            [
                'name' => 'sales_executive',
                'display_name' => 'Sales Executive',
                'description' => 'Create quotation, invoice',
                'permissions' => ['sales.create', 'quotation.create', 'invoice.create'],
            ],
            [
                'name' => 'sales_manager',
                'display_name' => 'Sales Manager',
                'description' => 'Approve discounts, credit sales',
                'permissions' => ['sales.approve', 'discount.approve'],
            ],
            [
                'name' => 'store_keeper',
                'display_name' => 'Store Keeper',
                'description' => 'Stock issue, damage stock',
                'permissions' => ['stock.manage', 'stock.issue'],
            ],
            [
                'name' => 'service_engineer',
                'display_name' => 'Service Engineer',
                'description' => 'Job Card execution',
                'permissions' => ['jobcard.create', 'jobcard.update'],
            ],
            [
                'name' => 'accounts_executive',
                'display_name' => 'Accounts Executive',
                'description' => 'Expenses, payments',
                'permissions' => ['expense.create', 'payment.create'],
            ],
            [
                'name' => 'finance_manager',
                'display_name' => 'Finance Manager',
                'description' => 'Invoice, VAT, credit approvals',
                'permissions' => ['invoice.approve', 'vat.manage', 'credit.approve'],
            ],
            [
                'name' => 'cfo',
                'display_name' => 'CFO / Owner',
                'description' => 'Final authority',
                'permissions' => ['*'], // All permissions
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
