<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisitorCategory;
use Illuminate\Support\Str;

class VisitorCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'VIP',
                'slug' => 'vip',
                'color' => '#8b5cf6',
                'description' => 'Very Important Persons',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Regular',
                'slug' => 'regular',
                'color' => '#0099ff',
                'description' => 'Regular visitors',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Contractor',
                'slug' => 'contractor',
                'color' => '#f59e0b',
                'description' => 'Contractors and service providers',
                'priority' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Vendor',
                'slug' => 'vendor',
                'color' => '#10b981',
                'description' => 'Vendors and suppliers',
                'priority' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'color' => '#6b7280',
                'description' => 'General guests',
                'priority' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            VisitorCategory::create($category);
        }
    }
}
