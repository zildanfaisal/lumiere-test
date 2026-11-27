<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@lumiere.test'], ['name' => 'Admin Lumière', 'password' => 'password', 'role' => 'admin']);
        User::updateOrCreate(['email' => 'pembeli@lumiere.test'], ['name' => 'Pembeli Demo', 'password' => 'password', 'role' => 'pembeli']);
        $products = [
            ['Cleansing Balm', 'Pembersih lembut untuk mengangkat sunscreen dan riasan.', [['Almond', 129000, 20], ['Tutti Fruity', 135000, 15]]],
            ['Body Scrub', 'Eksfoliasi tubuh dengan butiran halus dan aroma menenangkan.', [['Almond', 99000, 18], ['Tutti Fruity', 105000, 12]]],
            ['Clay Cleanser', 'Pembersih berbasis clay untuk rasa bersih tanpa tertarik.', [['Almond', 119000, 14], ['Tutti Fruity', 119000, 16]]],
        ];
        foreach ($products as [$name, $description, $variants]) {
            $product = Product::updateOrCreate(['name' => $name], ['description' => $description]);
            foreach ($variants as [$variantName, $price, $stock]) {
                $product->variants()->updateOrCreate(['name' => $variantName], ['price' => $price, 'stock' => $stock]);
            }
        }
    }
}
