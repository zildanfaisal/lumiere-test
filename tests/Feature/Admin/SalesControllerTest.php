<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_only_products_from_paid_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $product = Product::create(['name' => 'Cleansing Balm']);
        $variant = $product->variants()->create([
            'name' => 'Almond',
            'price' => 100000,
            'stock' => 5,
        ]);
        $paidOrder = $buyer->orders()->create([
            'status' => Order::PAID,
            'total' => 200000,
            'paid_at' => now(),
        ]);
        $paidOrder->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => 'Cleansing Balm Terjual',
            'variant_name' => 'Almond',
            'unit_price' => 100000,
            'quantity' => 2,
            'subtotal' => 200000,
        ]);
        $pendingOrder = $buyer->orders()->create([
            'status' => Order::PENDING,
            'total' => 100000,
        ]);
        $pendingOrder->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => 'Produk Belum Dibayar',
            'variant_name' => 'Almond',
            'unit_price' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.sales.index'));

        $response
            ->assertOk()
            ->assertSee('Cleansing Balm Terjual')
            ->assertSee('1 pesanan', false)
            ->assertDontSee('Produk Belum Dibayar');
    }

    public function test_buyer_cannot_see_sales_history(): void
    {
        $buyer = User::factory()->create(['role' => 'pembeli']);

        $response = $this->actingAs($buyer)->get(route('admin.sales.index'));

        $response->assertForbidden();
    }
}
