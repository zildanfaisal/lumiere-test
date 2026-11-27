<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return view('cart.index', ['items' => $this->items($request)]);
    }

    public function store(CartRequest $request)
    {
        $data = $request->validated();
        $variant = ProductVariant::findOrFail($data['variant_id']);
        if ($variant->stock < $data['quantity']) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }
        $cart = $request->session()->get('cart', []);
        $cart[$variant->id] = min(($cart[$variant->id] ?? 0) + $data['quantity'], $variant->stock);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate(['quantities' => ['required', 'array'], 'quantities.*' => ['required', 'integer', 'min:0', 'max:99']]);
        $cart = [];
        foreach ($validated['quantities'] as $id => $quantity) {
            $variant = ProductVariant::find($id);
            if ($variant && $quantity > 0) {
                if ($quantity > $variant->stock) {
                    return back()->with('error', "Stok {$variant->product->name} — {$variant->name} hanya {$variant->stock}.");
                }
                $cart[$id] = $quantity;
            }
        }
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Request $request, int $variantId): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$variantId]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public static function items(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        return ProductVariant::with('product')->whereIn('id', array_keys($cart))->get()->map(function ($variant) use ($cart) {
            $variant->cart_quantity = $cart[$variant->id];

            return $variant;
        });
    }
}
