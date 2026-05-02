<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\ServicePrice;

class AiServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create AI Service
        $service = Service::updateOrCreate(
            ['name' => 'AI Services'],
            [
                'description' => 'Artificial Intelligence API Services',
                'is_active' => true
            ]
        );

        // 2. Create AI Field (DeepSeek)
        $field = ServiceField::updateOrCreate(
            ['field_code' => '700'],
            [
                'service_id' => $service->id,
                'field_name' => 'AI Chat (DeepSeek)',
                'description' => 'DeepSeek AI Chat API Integration',
                'base_price' => 50.00,
                'is_active' => true
            ]
        );

        // 3. Define Role-Based Prices
        $prices = [
            'personal' => 50.00,
            'agent' => 45.00,
            'partner' => 40.00,
            'business' => 35.00,
        ];

        foreach ($prices as $role => $price) {
            ServicePrice::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'service_fields_id' => $field->id,
                    'user_type' => $role
                ],
                ['price' => $price]
            );
        }
    }
}
