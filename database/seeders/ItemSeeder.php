<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        
        if (!$company) {
            return;
        }
        
        $items = [
            [
                'company_id' => $company->id,
                'item_name' => 'HP LaserJet Pro M404n Printer',
                'item_type' => 'Product',
                'stock_type' => 'Stock',
                'brand' => 'HP',
                'oem_part_no' => 'W1A52A',
                'duplicate_part_no' => 'HP-M404N',
                'unit' => 'PC',
                'sale_price' => 250.000,
                'min_sale_price' => 230.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Professional monochrome laser printer',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'Dell OptiPlex 7090 Desktop',
                'item_type' => 'Product',
                'stock_type' => 'Stock',
                'brand' => 'Dell',
                'oem_part_no' => 'N008O7090SFF',
                'duplicate_part_no' => 'DELL-7090',
                'unit' => 'PC',
                'sale_price' => 850.000,
                'min_sale_price' => 800.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Business desktop computer with Intel i5',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'Microsoft Office 365 Business Standard',
                'item_type' => 'Service',
                'stock_type' => 'Service',
                'brand' => 'Microsoft',
                'oem_part_no' => 'KLQ-00378',
                'duplicate_part_no' => 'O365-BIZ',
                'unit' => 'License',
                'sale_price' => 12.500,
                'min_sale_price' => 11.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Monthly subscription for Office 365 Business',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'CAT 6 Ethernet Cable (305m)',
                'item_type' => 'Product',
                'stock_type' => 'Stock',
                'brand' => 'Panduit',
                'oem_part_no' => 'PUP6004IG-CY',
                'duplicate_part_no' => 'CAT6-305M',
                'unit' => 'Roll',
                'sale_price' => 185.000,
                'min_sale_price' => 175.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Category 6 UTP network cable, 305 meter roll',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'Installation & Configuration Service',
                'item_type' => 'Service',
                'stock_type' => 'Service',
                'brand' => 'BluRay',
                'oem_part_no' => 'SRV-INSTALL-001',
                'duplicate_part_no' => 'INSTALL-SRV',
                'unit' => 'Hour',
                'sale_price' => 45.000,
                'min_sale_price' => 40.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Professional installation and configuration service',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'APC UPS Smart-UPS 1500VA',
                'item_type' => 'Product',
                'stock_type' => 'Stock',
                'brand' => 'APC',
                'oem_part_no' => 'SMT1500I',
                'duplicate_part_no' => 'APC-1500VA',
                'unit' => 'PC',
                'sale_price' => 420.000,
                'min_sale_price' => 400.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Uninterruptible Power Supply with LCD display',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'Cisco Catalyst 2960-X Switch 24 Port',
                'item_type' => 'Product',
                'stock_type' => 'Stock',
                'brand' => 'Cisco',
                'oem_part_no' => 'WS-C2960X-24TS-L',
                'duplicate_part_no' => 'CISCO-2960X-24P',
                'unit' => 'PC',
                'sale_price' => 1250.000,
                'min_sale_price' => 1200.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => '24-port Gigabit managed switch',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'item_name' => 'Annual Maintenance Contract',
                'item_type' => 'Service',
                'stock_type' => 'Service',
                'brand' => 'BluRay',
                'oem_part_no' => 'AMC-COMP-001',
                'duplicate_part_no' => 'AMC-COMPREHENSIVE',
                'unit' => 'Year',
                'sale_price' => 500.000,
                'min_sale_price' => 450.000,
                'vat_applicable' => true,
                'vat_rate' => 5.00,
                'description' => 'Comprehensive annual maintenance contract',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
