@extends('layouts.app', ['title' => 'Katalog — Lumière Skin'])

@section('content')
    <div class="mb-10 max-w-2xl">
        <h1 class="page-title">Perawatan yang terasa personal.</h1>
        <p class="mt-3 text-lg text-stone-600">
            Pilih produk dan aroma yang cocok untuk rutinitasmu.
        </p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
            <article class="flex flex-col rounded-2xl bg-white p-6 shadow-[0_10px_30px_rgba(23,63,53,.08)]">
                <div class="mb-6 h-2 w-16 rounded-full bg-[#d59672]"></div>

                <h2 class="font-serif text-3xl text-[#173f35]">{{ $product->name }}</h2>
                <p class="mt-2 min-h-12 text-stone-600">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <label class="block">
                        <span class="label">Varian</span>
                        <select name="variant_id" class="input" required>
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->id }}" @disabled($variant->stock === 0)>
                                    {{ $variant->name }} —
                                    Rp {{ number_format($variant->price, 0, ',', '.') }}
                                    (stok {{ $variant->stock }})
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="label">Jumlah</span>
                        <input
                            type="number"
                            name="quantity"
                            value="1"
                            min="1"
                            max="99"
                            class="input"
                        >
                    </label>

                    <button
                        class="btn-primary w-full"
                        @disabled($product->variants->sum('stock') === 0)
                    >
                        Tambah ke keranjang
                    </button>
                </form>
            </article>
        @empty
            <p>Belum ada produk tersedia.</p>
        @endforelse
    </div>
@endsection
