<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Perfect for small websites and startups',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Monthly Billing',
                    'Basic Support',
                    'SSL Certificate',
                    'Website Builder',
                    '99.9% Uptime Guarantee'
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Pro Plan',
                'description' => 'Ideal for growing businesses',
                'price' => 19.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Monthly Billing',
                    'Priority Support',
                    'SSL Certificate',
                    'Advanced Website Builder',
                    'Daily Backups',
                    '99.9% Uptime Guarantee'
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Business Plan',
                'description' => 'For established businesses and agencies',
                'price' => 39.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Monthly Billing',
                    '24/7 Phone Support',
                    'Premium SSL Certificate',
                    'Advanced Website Builder',
                    'Daily Backups',
                    'Advanced Analytics',
                    '99.9% Uptime Guarantee',
                    'Custom Domain'
                ],
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Custom solutions for large enterprises',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Monthly Billing',
                    'Dedicated Account Manager',
                    'Premium SSL Certificate',
                    'Enterprise Website Builder',
                    'Real-time Backups',
                    'Advanced Analytics',
                    'Custom Integrations',
                    '99.9% Uptime Guarantee',
                    'Priority Support'
                ],
                'is_active' => true,
                'sort_order' => 4
            ]
        ];

        foreach ($plans as $plan) {
            \App\Models\SubscriptionPlan::create($plan);
        }
    }
}
