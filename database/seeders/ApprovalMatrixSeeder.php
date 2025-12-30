<?php

namespace Database\Seeders;

use App\Models\ApprovalMatrix;
use Illuminate\Database\Seeder;

class ApprovalMatrixSeeder extends Seeder
{
    public function run(): void
    {
        $approvalMatrix = [
            // Quotation Approvals
            ['module_name' => 'quotation', 'role_required' => 'sales_manager', 'min_amount' => 0, 'max_amount' => null, 'sequence_order' => 1],
            
            // Sales Invoice Approvals
            ['module_name' => 'sales_invoice', 'role_required' => 'finance_manager', 'min_amount' => 0, 'max_amount' => 10000, 'sequence_order' => 1],
            ['module_name' => 'sales_invoice', 'role_required' => 'cfo', 'min_amount' => 10000, 'max_amount' => null, 'sequence_order' => 2],
            
            // Purchase Order Approvals
            ['module_name' => 'purchase_order', 'role_required' => 'purchase_manager', 'min_amount' => 0, 'max_amount' => 5000, 'sequence_order' => 1],
            ['module_name' => 'purchase_order', 'role_required' => 'finance_manager', 'min_amount' => 5000, 'max_amount' => null, 'sequence_order' => 2],
            
            // GRN Approvals
            ['module_name' => 'grn', 'role_required' => 'purchase_manager', 'min_amount' => 0, 'max_amount' => null, 'sequence_order' => 1],
            
            // Purchase Invoice Approvals
            ['module_name' => 'purchase_invoice', 'role_required' => 'finance_manager', 'min_amount' => 0, 'max_amount' => null, 'sequence_order' => 1],
            
            // Expense Approvals
            ['module_name' => 'expense', 'role_required' => 'finance_manager', 'min_amount' => 0, 'max_amount' => 1000, 'sequence_order' => 1],
            ['module_name' => 'expense', 'role_required' => 'cfo', 'min_amount' => 1000, 'max_amount' => null, 'sequence_order' => 2],
            
            // Job Card Approvals
            ['module_name' => 'job_card', 'role_required' => 'sales_manager', 'min_amount' => 0, 'max_amount' => null, 'sequence_order' => 1],
            
            // AMC Contract Approvals
            ['module_name' => 'amc_contract', 'role_required' => 'sales_manager', 'min_amount' => 0, 'max_amount' => 5000, 'sequence_order' => 1],
            ['module_name' => 'amc_contract', 'role_required' => 'finance_manager', 'min_amount' => 5000, 'max_amount' => 20000, 'sequence_order' => 2],
            ['module_name' => 'amc_contract', 'role_required' => 'cfo', 'min_amount' => 20000, 'max_amount' => null, 'sequence_order' => 3],
        ];

        foreach ($approvalMatrix as $matrix) {
            ApprovalMatrix::updateOrCreate(
                [
                    'module_name' => $matrix['module_name'],
                    'role_required' => $matrix['role_required'],
                    'min_amount' => $matrix['min_amount']
                ],
                $matrix
            );
        }
    }
}
