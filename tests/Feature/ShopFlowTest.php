<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopFlowTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(): User
    {
        return User::factory()->create(['role' => 'pembeli']);
    }

    private function variant(int $stock = 5)
    {
        $product = Product::create(['name' => 'Cleansing Balm', 'description' => 'Lembut']);

        return $product->variants()->create(['name' => 'Almond', 'price' => 100000, 'stock' => $stock]);
    }

    public function test_buyer_cannot_access_admin_pages(): void
    {
        $this->actingAs($this->buyer())->get('/admin/products')->assertForbidden();
    }

    public function test_root_redirects_authenticated_users_by_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/')->assertRedirect(route('admin.products.index'));

        $this->actingAs($this->buyer())->get('/')->assertRedirect(route('catalog.index'));
    }

    public function test_admin_login_ignores_buyer_intended_url(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@lumiere.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->withSession(['url.intended' => route('catalog.index')])
            ->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.products.index'));
    }

    public function test_admin_can_create_product_with_variant(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Clay Cleanser', 'description' => 'Bersih',
            'variants' => [['name' => 'Tutti Fruity', 'price' => 125000, 'stock' => 8]],
        ])->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Clay Cleanser']);
        $this->assertDatabaseHas('product_variants', ['name' => 'Tutti Fruity', 'stock' => 8]);
    }

    public function test_checkout_does_not_reduce_stock_and_payment_does(): void
    {
        $buyer = $this->buyer();
        $variant = $this->variant();
        $response = $this->actingAs($buyer)->withSession(['cart' => [$variant->id => 2]])->post(route('orders.store'));
        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(5, $variant->fresh()->stock);
        $this->post(route('orders.pay', $order))->assertSessionHas('success');
        $this->assertSame(3, $variant->fresh()->stock);
        $this->assertSame(Order::PAID, $order->fresh()->status);
    }

    public function test_payment_is_rejected_when_stock_is_insufficient(): void
    {
        $buyer = $this->buyer();
        $variant = $this->variant(1);
        $order = $buyer->orders()->create(['status' => Order::PENDING, 'total' => 200000]);
        $order->items()->create(['product_variant_id' => $variant->id, 'product_name' => 'Cleansing Balm', 'variant_name' => 'Almond', 'unit_price' => 100000, 'quantity' => 2, 'subtotal' => 200000]);
        $this->actingAs($buyer)->post(route('orders.pay', $order))->assertSessionHasErrors('stock');
        $this->assertSame(1, $variant->fresh()->stock);
        $this->assertSame(Order::PENDING, $order->fresh()->status);
    }

    public function test_cancellation_does_not_change_stock(): void
    {
        $buyer = $this->buyer();
        $variant = $this->variant();
        $order = $buyer->orders()->create(['status' => Order::PENDING, 'total' => 100000]);
        $this->actingAs($buyer)->post(route('orders.cancel', $order))->assertSessionHas('success');
        $this->assertSame(Order::CANCELLED, $order->fresh()->status);
        $this->assertSame(5, $variant->fresh()->stock);
    }
}
