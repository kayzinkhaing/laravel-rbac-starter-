<?php

namespace Database\Seeders;

use App\Models\MasterDataCategory;
use App\Models\MasterDataItem;
use Illuminate\Database\Seeder;
use RuntimeException;

class MasterDataItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'status' => [
                ['key' => 'active', 'value' => 'Active', 'label' => 'Active'],
                ['key' => 'inactive', 'value' => 'Inactive', 'label' => 'Inactive'],
                ['key' => 'pending', 'value' => 'Pending', 'label' => 'Pending'],
                ['key' => 'approved', 'value' => 'Approved', 'label' => 'Approved'],
                ['key' => 'rejected', 'value' => 'Rejected', 'label' => 'Rejected'],
                ['key' => 'archived', 'value' => 'Archived', 'label' => 'Archived'],
            ],

            'order-status' => [
                ['key' => 'pending', 'value' => 'Pending', 'label' => 'Pending'],
                ['key' => 'confirmed', 'value' => 'Confirmed', 'label' => 'Confirmed'],
                ['key' => 'processing', 'value' => 'Processing', 'label' => 'Processing'],
                ['key' => 'shipped', 'value' => 'Shipped', 'label' => 'Shipped'],
                ['key' => 'completed', 'value' => 'Completed', 'label' => 'Completed'],
                ['key' => 'cancelled', 'value' => 'Cancelled', 'label' => 'Cancelled'],
                ['key' => 'refunded', 'value' => 'Refunded', 'label' => 'Refunded'],
            ],

            'payment-status' => [
                ['key' => 'pending', 'value' => 'Pending', 'label' => 'Pending'],
                ['key' => 'processing', 'value' => 'Processing', 'label' => 'Processing'],
                ['key' => 'paid', 'value' => 'Paid', 'label' => 'Paid'],
                ['key' => 'failed', 'value' => 'Failed', 'label' => 'Failed'],
                ['key' => 'cancelled', 'value' => 'Cancelled', 'label' => 'Cancelled'],
                ['key' => 'refunded', 'value' => 'Refunded', 'label' => 'Refunded'],
            ],

            'payment-method' => [
                ['key' => 'cash', 'value' => 'Cash', 'label' => 'Cash'],
                ['key' => 'card', 'value' => 'Credit / Debit Card', 'label' => 'Card'],
                ['key' => 'bank-transfer', 'value' => 'Bank Transfer', 'label' => 'Bank Transfer'],
                ['key' => 'mobile-wallet', 'value' => 'Mobile Wallet', 'label' => 'Mobile Wallet'],
                ['key' => 'online-payment', 'value' => 'Online Payment', 'label' => 'Online Payment'],
            ],

            'shipping-status' => [
                ['key' => 'pending', 'value' => 'Pending', 'label' => 'Pending'],
                ['key' => 'ready-for-pickup', 'value' => 'Ready for Pickup', 'label' => 'Ready for Pickup'],
                ['key' => 'picked-up', 'value' => 'Picked Up', 'label' => 'Picked Up'],
                ['key' => 'in-transit', 'value' => 'In Transit', 'label' => 'In Transit'],
                ['key' => 'out-for-delivery', 'value' => 'Out for Delivery', 'label' => 'Out for Delivery'],
                ['key' => 'delivered', 'value' => 'Delivered', 'label' => 'Delivered'],
                ['key' => 'failed', 'value' => 'Delivery Failed', 'label' => 'Delivery Failed'],
                ['key' => 'returned', 'value' => 'Returned', 'label' => 'Returned'],
            ],

            'priority' => [
                ['key' => 'low', 'value' => 'Low', 'label' => 'Low'],
                ['key' => 'medium', 'value' => 'Medium', 'label' => 'Medium'],
                ['key' => 'high', 'value' => 'High', 'label' => 'High'],
                ['key' => 'urgent', 'value' => 'Urgent', 'label' => 'Urgent'],
            ],

            'gender' => [
                ['key' => 'male', 'value' => 'Male', 'label' => 'Male'],
                ['key' => 'female', 'value' => 'Female', 'label' => 'Female'],
                ['key' => 'other', 'value' => 'Other', 'label' => 'Other'],
                ['key' => 'prefer-not-to-say', 'value' => 'Prefer not to say', 'label' => 'Prefer not to say'],
            ],

            'yes-no' => [
                ['key' => 'yes', 'value' => 'Yes', 'label' => 'Yes'],
                ['key' => 'no', 'value' => 'No', 'label' => 'No'],
            ],
        ];

        foreach ($items as $categoryKey => $categoryItems) {
            $category = MasterDataCategory::where('key', $categoryKey)->first();

            if (!$category) {
                throw new RuntimeException(
                    "Master Data Category '{$categoryKey}' does not exist. Run MasterDataCategorySeeder first."
                );
            }

            foreach ($categoryItems as $index => $item) {
                MasterDataItem::firstOrCreate(
                    [
                        'master_data_category_id' => $category->id,
                        'key' => $item['key'],
                    ],
                    [
                        'value' => $item['value'],
                        'label' => $item['label'],
                        'description' => null,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
