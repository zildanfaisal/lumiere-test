<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_remove_one_item_from_cart(): void
    {
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $product = Product::create(['name' => 'Cleansing Balm']);
        $almond = $product->variants()->create([
            'name' => 'Almond',
            'price' => 100000,
            'stock' => 5,
        ]);
        $fruity = $product->variants()->create([
            'name' => 'Tutti Fruity',
            'price' => 110000,
            'stock' => 5,
        ]);

        $response = $this->actingAs($buyer)
            ->withSession(['cart' => [$almond->id => 2, $fruity->id => 1]])
            ->delete(route('cart.destroy', $almond->id));

        $response
            ->assertRedirect()
            ->assertSessionHas('success', 'Produk dihapus dari keranjang.')
            ->assertSessionHas('cart', [$fruity->id => 1]);
    }

    public function test_admin_cannot_remove_buyer_cart_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->withSession(['cart' => [123 => 1]])
            ->delete(route('cart.destroy', 123));

        $response->assertForbidden();
        $response->assertSessionHas('cart', [123 => 1]);
    }
}
