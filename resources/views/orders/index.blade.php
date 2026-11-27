@extends('layouts.app', ['title' => 'Pesanan — Lumière Skin'])

@section('content')
    <h1 class="page-title">Pesanan saya</h1>

    <div class="table-wrap mt-8">
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="font-semibold">
                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td><span class="status">{{ $order->status }}</span></td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            <a class="link" href="{{ route('orders.show', $order) }}">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-stone-500">
                            Belum ada pesanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
