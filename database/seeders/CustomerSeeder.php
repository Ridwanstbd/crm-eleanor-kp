<?php

namespace Database\Seeders;

use App\Models\Product;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = collect();

        $customers = $customers->merge(
            CustomerFactory::factory()
                ->count(15)
                ->recentMessage()
                ->indonesianPhone()
                ->create()
        );

        $customers = $customers->merge(
            CustomerFactory::factory()
                ->count(20)
                ->oldMessage()
                ->indonesianPhone()
                ->create()
        );

        $customers = $customers->merge(
            CustomerFactory::factory()
                ->count(10)
                ->noMessage()
                ->indonesianPhone()
                ->create()
        );

        $customers = $customers->merge(
            CustomerFactory::factory()
                ->count(5)
                ->create()
        );

        if (class_exists(\App\Models\CustomerGroup::class)) {
            $this->attachCustomerGroups($customers);
        }

        if (class_exists(\App\Models\Product::class)) {
            $this->attachProductPurchases($customers);
        }
    }

    /**
     * Attach customers ke customer groups (jika ada)
     */
    private function attachCustomerGroups($customers): void
    {
        $groups = CustomerGroup::all();
        
        if ($groups->isNotEmpty()) {
            $customers->each(function ($customer) use ($groups) {
                // Setiap customer bisa masuk ke 0-3 grup secara random
                $randomGroups = $groups->random(rand(0, min(3, $groups->count())));
                $customer->groups()->attach($randomGroups->pluck('id'));
            });
        }
    }

    /**
     * Attach product purchases ke customers (jika ada)
     */
    private function attachProductPurchases($customers): void
    {
        $products = Product::all();
        
        if ($products->isNotEmpty()) {
            $customers->each(function ($customer) use ($products) {
                $randomProducts = $products->random(rand(0, min(5, $products->count())));
                
                $purchaseData = [];
                foreach ($randomProducts as $product) {
                    $purchaseData[$product->id] = [
                        'last_purchase_quantity' => rand(1, 10)
                    ];
                }
                
                $customer->purchases()->attach($purchaseData);
            });
        }
    }
}
