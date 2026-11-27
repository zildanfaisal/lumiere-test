@extends('layouts.app', ['title' => 'Keranjang — Lumière Skin'])

@section('content')
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Keranjang</h1>
            <p class="mt-2 text-stone-600">Atur jumlah sebelum membuat pesanan.</p>
        </div>

        <a class="link" href="{{ route('catalog.index') }}">Lanjut belanja</a>
    </div>

    @if ($items->isEmpty())
        <div class="mt-10 rounded-2xl bg-white p-10 text-center">
            <p class="text-lg">Keranjangmu masih kosong.</p>
            <a class="btn-primary mt-5 inline-block" href="{{ route('catalog.index') }}">
                Jelajahi katalog
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('cart.update') }}" class="mt-8">
            @csrf
            @method('PUT')

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    <span class="block text-sm text-stone-500">{{ $item->name }}</span>
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>
                                    <input
                                        class="input w-24"
                                        type="number"
                                        min="0"
                                        max="{{ $item->stock }}"
                                        name="quantities[{{ $item->id }}]"
                                        value="{{ $item->cart_quantity }}"
                                    >
                                </td>
                                <td>
                                    Rp {{ number_format($item->price * $item->cart_quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-semibold">Total</td>
                            <td class="font-bold">
                                Rp {{ number_format($items->sum(fn ($item) => $item->price * $item->cart_quantity), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button class="btn-secondary">Perbarui keranjang</button>
            </div>
        </form>

        <form method="POST" action="{{ route('orders.store') }}" class="mt-3 flex justify-end">
            @csrf

            <button class="btn-primary">Checkout</button>
        </form>
    @endif
@endsection
