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

        // 2. Create AI Field (DeepSeek - Per Request, left active if legacy billing is needed, but we focus on subscriptions)
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

        // 3. Create AI Subscription Plans
        $plans = [
            'basic' => [
                'code' => '701',
                'name' => 'AI Basic Subscription (500 Requests)',
                'description' => 'AI Chat access with 500 requests limit',
                'base_price' => 1500.00,
            ],
            'standard' => [
                'code' => '702',
                'name' => 'AI Standard Subscription (1000 Requests)',
                'description' => 'AI Chat access with 1000 requests limit',
                'base_price' => 3000.00,
            ],
            'premium' => [
                'code' => '703',
                'name' => 'AI Premium Subscription (Unlimited)',
                'description' => 'Unlimited AI Chat access',
                'base_price' => 7000.00,
            ]
        ];

        foreach ($plans as $planKey => $planDetails) {
            ServiceField::updateOrCreate(
                ['field_code' => $planDetails['code']],
                [
                    'service_id' => $service->id,
                    'field_name' => $planDetails['name'],
                    'description' => $planDetails['description'],
                    'base_price' => $planDetails['base_price'],
                    'is_active' => true
                ]
            );
        }
    }
}
