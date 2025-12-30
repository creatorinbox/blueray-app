<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSubCategorySeeder extends Seeder
{
    public function run()
    {
        $subCategories = [
            // Office Supplies (category_id: 1)
            ['category_id' => 1, 'sub_category_name' => 'Stationery', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 1, 'sub_category_name' => 'Printing & Copies', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 1, 'sub_category_name' => 'Computer Supplies', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Travel & Transportation (category_id: 2)
            ['category_id' => 2, 'sub_category_name' => 'Flight Tickets', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 2, 'sub_category_name' => 'Hotel Accommodation', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 2, 'sub_category_name' => 'Local Transportation', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Meals & Entertainment (category_id: 3)
            ['category_id' => 3, 'sub_category_name' => 'Client Meals', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'sub_category_name' => 'Team Lunch', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'sub_category_name' => 'Coffee & Refreshments', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Utilities (category_id: 4)
            ['category_id' => 4, 'sub_category_name' => 'Electricity', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 4, 'sub_category_name' => 'Internet & Phone', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 4, 'sub_category_name' => 'Water & Sewage', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Maintenance & Repairs (category_id: 5)
            ['category_id' => 5, 'sub_category_name' => 'Equipment Repair', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 5, 'sub_category_name' => 'Building Maintenance', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 5, 'sub_category_name' => 'Software Maintenance', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Marketing & Advertising (category_id: 6)
            ['category_id' => 6, 'sub_category_name' => 'Digital Marketing', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 6, 'sub_category_name' => 'Print Advertising', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 6, 'sub_category_name' => 'Trade Shows', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Professional Services (category_id: 7)
            ['category_id' => 7, 'sub_category_name' => 'Legal Fees', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 7, 'sub_category_name' => 'Accounting Fees', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 7, 'sub_category_name' => 'Consulting', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Vehicle & Transport (category_id: 8)
            ['category_id' => 8, 'sub_category_name' => 'Fuel', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 8, 'sub_category_name' => 'Vehicle Maintenance', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 8, 'sub_category_name' => 'Vehicle Insurance', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('expense_sub_categories')->insert($subCategories);
    }
}