@extends('layouts.app', ['title' => 'Riwayat Penjualan — Lumière Skin'])

@section('content')
    <div class="max-w-3xl">
        <h1 class="page-title">Riwayat penjualan</h1>
        <p class="mt-2 text-stone-600">
            Produk dari pesanan yang telah dibayar, diurutkan dari pembayaran terbaru.
        </p>

        <p class="mt-5 text-sm text-stone-700">
            <strong class="text-[#173f35]">{{ number_format($orderCount, 0, ',', '.') }} pesanan</strong>
            telah selesai dengan pendapatan
            <strong class="text-[#173f35]">Rp {{ number_format($revenue, 0, ',', '.') }}</strong>.
        </p>
    </div>

    <div class="table-wrap mt-8">
        <table>
            <thead>
                <tr>
                    <th>Waktu bayar</th>
                    <th>Pesanan</th>
                    <th>Pembeli</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $order)
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                @if ($loop->first)
                                    {{ $order->paid_at->format('d M Y, H:i') }}
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    <span class="font-semibold tabular-nums">
                                        #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="block text-sm text-stone-500">
                                        Total Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($loop->first)
                                    <span class="font-medium">{{ $order->user->name }}</span>
                                    <span class="block text-sm text-stone-500">{{ $order->user->email }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-medium">{{ $item->product_name }}</span>
                                <span class="block text-sm text-stone-500">{{ $item->variant_name }}</span>
                            </td>
                            <td>{{ number_format($item->quantity, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-500">
                            Belum ada produk yang terjual.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($sales->hasPages())
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    @endif
@endsection
