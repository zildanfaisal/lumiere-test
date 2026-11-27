@extends('layouts.app', ['title' => 'Kelola Produk — Lumière Skin'])

@section('content')
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Kelola produk</h1>
            <p class="mt-2 text-stone-600">Harga dan stok diatur per varian.</p>
        </div>

        <a class="btn-primary" href="{{ route('admin.products.create') }}">
            Tambah produk
        </a>
    </div>

    <div class="table-wrap mt-8">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Varian</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    @foreach ($product->variants as $variant)
                        <tr>
                            <td>
                                @if ($loop->first)
                                    <strong>{{ $product->name }}</strong>
                                    <span class="block max-w-xs text-sm text-stone-500">
                                        {{ $product->description }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $variant->name }}</td>
                            <td>Rp {{ number_format($variant->price, 0, ',', '.') }}</td>
                            <td>{{ $variant->stock }}</td>
                            <td>
                                @if ($loop->first)
                                    <div class="flex gap-3">
                                        <a class="link" href="{{ route('admin.products.edit', $product) }}">
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.products.destroy', $product) }}"
                                            onsubmit="return confirm('Hapus produk ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button class="link-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">Belum ada produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
