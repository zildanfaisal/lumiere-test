<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('orders.index', ['orders' => $request->user()->orders()->latest()->get()]);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        return view('orders.show', ['order' => $order->load('items')]);
    }

    public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if ($cart === []) {
            return back()->with('error', 'Keranjang masih kosong.');
        }
        $variants = ProductVariant::with('product')->whereIn('id', array_keys($cart))->get();
        if ($variants->count() !== count($cart)) {
            return back()->with('error', 'Sebagian item keranjang sudah tidak tersedia.');
        }
        $order = DB::transaction(function () use ($request, $variants, $cart) {
            $total = $variants->sum(fn ($v) => $v->price * $cart[$v->id]);
            $order = $request->user()->orders()->create(['status' => Order::PENDING, 'total' => $total]);
            foreach ($variants as $variant) {
                $order->items()->create([
                    'product_variant_id' => $variant->id, 'product_name' => $variant->product->name,
                    'variant_name' => $variant->name, 'unit_price' => $variant->price,
                    'quantity' => $cart[$variant->id], 'subtotal' => $variant->price * $cart[$variant->id],
                ]);
            }

            return $order;
        });
        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan dibuat. Stok baru berkurang setelah pembayaran berhasil.');
    }

    public function pay(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);
        try {
            DB::transaction(function () use ($order) {
                $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
                if ($lockedOrder->status !== Order::PENDING) {
                    throw ValidationException::withMessages(['order' => 'Pesanan ini tidak dapat dibayar karena statusnya bukan Menunggu Pembayaran.']);
                }
                foreach ($lockedOrder->items as $item) {
                    $variant = ProductVariant::lockForUpdate()->findOrFail($item->product_variant_id);
                    if ($variant->stock < $item->quantity) {
                        throw ValidationException::withMessages(['stock' => "Stok {$item->product_name} — {$item->variant_name} tidak cukup. Tersedia {$variant->stock}, dibutuhkan {$item->quantity}."]);
                    }
                    $variant->decrement('stock', $item->quantity);
                }
                $lockedOrder->update(['status' => Order::PAID, 'paid_at' => now()]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pembayaran berhasil disimulasikan dan stok telah dikurangi.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);
        if ($order->status !== Order::PENDING) {
            return back()->with('error', 'Hanya pesanan Menunggu Pembayaran yang dapat dibatalkan.');
        }
        $order->update(['status' => Order::CANCELLED, 'cancelled_at' => now()]);

        return back()->with('success', 'Pesanan dibatalkan. Stok tidak berubah.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 403);
    }
}
