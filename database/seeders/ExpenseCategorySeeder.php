<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'category_name' => 'Office Supplies',
                'description' => 'Office stationery and supplies',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Travel & Transportation',
                'description' => 'Travel expenses and transportation costs',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Meals & Entertainment',
                'description' => 'Client meals and entertainment expenses',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Utilities',
                'description' => 'Electricity, water, internet and other utilities',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Maintenance & Repairs',
                'description' => 'Equipment and facility maintenance',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Marketing & Advertising',
                'description' => 'Marketing and promotional expenses',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Professional Services',
                'description' => 'Legal, accounting and consulting fees',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Vehicle & Transport',
                'description' => 'Vehicle expenses and transportation',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('expense_categories')->insert($categories);
    }
}