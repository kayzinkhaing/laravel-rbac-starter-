<?php

namespace Database\Seeders;

use App\Models\MasterDataCategory;
use Illuminate\Database\Seeder;

class MasterDataCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'key' => 'status',
                'name' => 'General Status',
                'description' => 'Common status values for application records.',
                'sort_order' => 1,
            ],
            [
                'key' => 'order-status',
                'name' => 'Order Status',
                'description' => 'Order processing statuses.',
                'sort_order' => 2,
            ],
            [
                'key' => 'payment-status',
                'name' => 'Payment Status',
                'description' => 'Payment transaction statuses.',
                'sort_order' => 3,
            ],
            [
                'key' => 'payment-method',
                'name' => 'Payment Method',
                'description' => 'Available payment methods.',
                'sort_order' => 4,
            ],
            [
                'key' => 'shipping-status',
                'name' => 'Shipping Status',
                'description' => 'Shipment and delivery statuses.',
                'sort_order' => 5,
            ],
            [
                'key' => 'priority',
                'name' => 'Priority',
                'description' => 'Common task and request priorities.',
                'sort_order' => 6,
            ],
            [
                'key' => 'gender',
                'name' => 'Gender',
                'description' => 'General gender options where required.',
                'sort_order' => 7,
            ],
            [
                'key' => 'yes-no',
                'name' => 'Yes / No',
                'description' => 'Common boolean selection options.',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            MasterDataCategory::firstOrCreate(
                ['key' => $category['key']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
