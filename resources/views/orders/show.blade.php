@extends('layouts.app', ['title' => 'Detail Pesanan — Lumière Skin'])

@section('content')
    <a class="link" href="{{ route('orders.index') }}">← Semua pesanan</a>

    <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">
                Pesanan #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="mt-2 text-stone-600">
                Dibuat {{ $order->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        <span class="status text-base">{{ $order->status }}</span>
    </div>

    <div class="table-wrap mt-8">
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
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            <span class="block text-sm text-stone-500">{{ $item->variant_name }}</span>
                        </td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right font-semibold">Total</td>
                    <td class="font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if ($order->status === \App\Models\Order::PENDING)
        <div class="mt-6 flex flex-wrap gap-3">
            <form method="POST" action="{{ route('orders.pay', $order) }}">
                @csrf

                <button class="btn-primary">Simulasi Bayar Berhasil</button>
            </form>

            <form method="POST" action="{{ route('orders.cancel', $order) }}">
                @csrf

                <button class="btn-danger">Simulasi Batalkan Pesanan</button>
            </form>
        </div>

        <p class="mt-3 text-sm text-stone-500">
            Stok diperiksa kembali dan baru dikurangi saat pembayaran berhasil.
        </p>
    @endif
@endsection
