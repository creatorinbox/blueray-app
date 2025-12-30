<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        
        if (!$company) {
            return;
        }
        
        $customers = [
            [
                'company_id' => $company->id,
                'customer_name' => 'Al-Rawabi Trading LLC',
                'trn' => 'OM1111111111',
                'phone' => '+968-2456-7890',
                'email' => 'info@alrawabi.om',
                'credit_limit' => 50000.00,
                'payment_terms_days' => 30,
                'address' => 'Muscat, Oman',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'customer_name' => 'Oman National Industries',
                'trn' => 'OM2222222222',
                'phone' => '+968-2567-8901',
                'email' => 'contact@oni.om',
                'credit_limit' => 75000.00,
                'payment_terms_days' => 45,
                'address' => 'Sohar, Oman',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'customer_name' => 'Gulf Equipment Rental',
                'trn' => 'OM3333333333',
                'phone' => '+968-2678-9012',
                'email' => 'sales@ger.om',
                'credit_limit' => 100000.00,
                'payment_terms_days' => 60,
                'address' => 'Salalah, Oman',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'customer_name' => 'Modern Construction Co.',
                'trn' => 'OM4444444444',
                'phone' => '+968-2789-0123',
                'email' => 'info@modernconst.om',
                'credit_limit' => 25000.00,
                'payment_terms_days' => 30,
                'address' => 'Nizwa, Oman',
                'is_active' => true,
            ],
            [
                'company_id' => $company->id,
                'customer_name' => 'Omani Tech Solutions',
                'trn' => 'OM5555555555',
                'phone' => '+968-2890-1234',
                'email' => 'support@omantech.om',
                'credit_limit' => 40000.00,
                'payment_terms_days' => 30,
                'address' => 'Muscat, Oman',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
