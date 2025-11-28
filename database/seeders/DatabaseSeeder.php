<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\Logistic;
use App\Models\Good;
use App\Models\OrderType;
use App\Models\IncomePriceRule;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@domino.com'],
            ['name' => 'System Admin', 'password' => Hash::make('1234'), 'mobile' => '09120000000', 'wallet' => 0, 'address'=>'Admin Address']
        );

        $providerA = User::create(['name' => 'Ali (Investor A)', 'email' => 'ali@provider.com', 'password' => Hash::make('password'), 'mobile' => '09121111111', 'wallet' => 0]);
        $providerB = User::create(['name' => 'Reza (Investor B)', 'email' => 'reza@provider.com', 'password' => Hash::make('password'), 'mobile' => '09122222222', 'wallet' => 0]);
        $driver = User::create(['name' => 'Hassan (Driver)', 'email' => 'hassan@driver.com', 'password' => Hash::make('password'), 'mobile' => '09123333333', 'wallet' => 0]);
        $referrer = User::create(['name' => 'Mina (Marketer)', 'email' => 'mina@referrer.com', 'password' => Hash::make('password'), 'mobile' => '09125555555', 'wallet' => 0]);
        $customer = User::create(['name' => 'Sara Customer', 'email' => 'sara@customer.com', 'password' => Hash::make('password'), 'mobile' => '09124444444', 'wallet' => 0]);

        // 2. Income Price Rules (Sum = 100%)
        // We define fallbacks so if a specific provider is missing, the money goes to the 'good_provider' (Item Owner).
        
        IncomePriceRule::create([
            'type' => 'good_provider', 
            'percentage' => 40,
            'fallback_type' => null // Base owner, usually has no fallback
        ]);
        
        IncomePriceRule::create([
            'type' => 'warehouse_provider', 
            'percentage' => 20, 
            'fallback_type' => 'good_provider' // If no warehouse, give share to good owner
        ]);

        IncomePriceRule::create([
            'type' => 'logistics_provider', 
            'percentage' => 20, 
            'fallback_type' => 'good_provider' // If no logistic driver, give to good owner
        ]);

        IncomePriceRule::create([
            'type' => 'referrer_provider', 
            'percentage' => 20, 
            'fallback_type' => 'good_provider' // If no referrer, good owner takes the profit
        ]);

        IncomePriceRule::create([
            'type' => 'delivery', 
            'percentage' => null,
            'fallback_type' => null 
        ]);
        
        // 3. Order Types (Durations)
        $daily = OrderType::create(['name' => 'Daily', 'duration_days' => 1]);
        $weekly = OrderType::create(['name' => 'Weekly', 'duration_days' => 7]);
        $monthly = OrderType::create(['name' => 'Monthly', 'duration_days' => 30]);

        // 4. Categories
        $catMobility = Category::create(['name' => 'Mobility', 'slug' => 'mobility', 'color' => '#3b82f6']);
        $catBeds = Category::create(['name' => 'Hospital Beds', 'slug' => 'beds', 'color' => '#10b981']);
        $catRespiratory = Category::create(['name' => 'Respiratory', 'slug' => 'respiratory', 'color' => '#f59e0b']);

        // 5. Warehouses & Logistics
        $warehouseCentral = Warehouse::create(['title' => 'Central Depot (Tehran)', 'description' => 'Main storage facility']);
        $warehouseWest = Warehouse::create(['title' => 'West Branch', 'description' => 'Quick access storage']);

        $logisticVan = Logistic::create(['name' => 'Van #44', 'description' => 'Toyota Hiace']);
        $logisticBike = Logistic::create(['name' => 'Motor Courier', 'description' => 'Fast delivery for small items']);

        // --- Attach Providers to Infrastructure ---
        
        // Warehouse Ownership: Provider A (100%)
        $warehouseCentral->providers()->attach($providerA->id, ['ownership_percent' => 100]);
        
        // Logistic Ownership: Driver (100%)
        $logisticVan->providers()->attach($driver->id, ['ownership_percent' => 100]);

        // 6. Goods (Inventory)
        
        // Good 1: Electric Wheelchair (Owned 50/50 by Provider A and B)
        $wheelchair = Good::create([
            'title' => 'Electric Wheelchair Model X',
            'code' => 'WC-E-101',
            'category_id' => $catMobility->id,
            'warehouse_id' => $warehouseCentral->id,
            'is_available' => true,
            'description' => 'Heavy duty electric wheelchair with long battery life.',
        ]);

        $wheelchair->providers()->attach([
            $providerA->id => ['ownership_percent' => 50],
            $providerB->id => ['ownership_percent' => 50],
        ]);

        // Pricing for Wheelchair
        $wheelchair->prices()->attach([
            $daily->id => ['price' => 500000],   // 500k Toman
            $weekly->id => ['price' => 3000000],  // 3m Toman
            $monthly->id => ['price' => 10000000], // 10m Toman
        ]);

        // Good 2: ICU Bed (Owned 100% by Provider B)
        $bed = Good::create([
            'title' => 'Full Electric ICU Bed',
            'code' => 'BED-ICU-202',
            'category_id' => $catBeds->id,
            'warehouse_id' => $warehouseCentral->id,
            'is_available' => true,
        ]);

        $bed->providers()->attach($providerB->id, ['ownership_percent' => 100]);

        $bed->prices()->attach([
            $daily->id => ['price' => 1000000],
            $monthly->id => ['price' => 25000000],
        ]);

        // 7. Create a Sample Order
        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_mobile' => $customer->mobile,
            'customer_address' => 'Tehran, Valiasr St, Alley 4, No 12',
            'status' => 'in-rent',
            'has_collateral' => true,
            'description' => 'Urgent delivery requested',
        ]);

        // Create Order Item (Simulating a full rental)
        // We include the referrer here
        $item = OrderItem::create([
            'order_id' => $order->id,
            
            // Item
            'good_id' => $wheelchair->id,
            'good_info' => $wheelchair->toArray(),
            
            // Warehouse
            'warehouse_id' => $warehouseCentral->id,
            'warehouse_info' => $warehouseCentral->toArray(),
            
            // Logistic
            'logistic_id' => $logisticVan->id,
            'logistic_info' => $logisticVan->toArray(),
            
            // Referrer
            'referrer_id' => $referrer->id,
            'referrer_info' => $referrer->toArray(),
            
            // Type & Price
            'order_type_id' => $monthly->id,
            'order_type_info' => $monthly->toArray(),
            'price' => 10000000, 
            
            // Dates
            'started_at' => now(),
            'ended_at' => now()->addDays(30),
        ]);

        $this->command->info('Rental System Seeded Successfully!');
        $this->command->info('Income Rules configured: Good(40%), Warehouse(20%), Logistic(15%), Referrer(10%), Delivery(15%) = 100%');
    }
}