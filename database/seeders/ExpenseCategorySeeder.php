<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fuel', 'description' => 'Vehicle fuel purchases', 'color' => '#dc3545'],
            ['name' => 'Salaries', 'description' => 'Staff salaries and wages', 'color' => '#28a745'],
            ['name' => 'Rent', 'description' => 'Office and warehouse rent', 'color' => '#007bff'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, internet', 'color' => '#17a2b8'],
            ['name' => 'Maintenance', 'description' => 'Vehicle and equipment maintenance', 'color' => '#ffc107'],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office supplies', 'color' => '#6c757d'],
            ['name' => 'Shipping Fees', 'description' => 'Courier and delivery charges', 'color' => '#6610f2'],
            ['name' => 'Insurance', 'description' => 'Vehicle and business insurance', 'color' => '#e83e8c'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotions', 'color' => '#fd7e14'],
            ['name' => 'Travel', 'description' => 'Business travel expenses', 'color' => '#20c997'],
            ['name' => 'Miscellaneous', 'description' => 'Other business expenses', 'color' => '#6f42c1'],
        ];

        DB::table('expense_categories')->insert($categories);
    }
}
