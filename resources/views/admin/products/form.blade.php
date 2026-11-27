@extends('layouts.app', ['title' => ($product->exists ? 'Edit' : 'Tambah').' Produk — Lumière Skin'])

@section('content')
    <a class="link" href="{{ route('admin.products.index') }}">← Kembali</a>

    <h1 class="page-title mt-5">
        {{ $product->exists ? 'Edit produk' : 'Tambah produk' }}
    </h1>

    <form
        method="POST"
        action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
        class="mt-8 space-y-8"
    >
        @csrf

        @if ($product->exists)
            @method('PUT')
        @endif

        <div class="rounded-2xl bg-white p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <label>
                    <span class="label">Nama produk</span>
                    <input
                        class="input"
                        name="name"
                        value="{{ old('name', $product->name) }}"
                        required
                    >
                </label>

                <label>
                    <span class="label">Deskripsi</span>
                    <textarea class="input" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                </label>
            </div>
        </div>

        <section>
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="font-serif text-3xl text-[#173f35]">Varian</h2>
                    <p class="text-sm text-stone-600">Minimal satu varian wajib tersedia.</p>
                </div>

                <button type="button" id="add-variant" class="btn-secondary">
                    Tambah varian
                </button>
            </div>

            @php
                $variants = old(
                    'variants',
                    $product->exists
                        ? $product->variants->toArray()
                        : [['name' => '', 'price' => '', 'stock' => '']],
                );
            @endphp

            <div id="variants" class="mt-4 space-y-3">
                @foreach ($variants as $index => $variant)
                    <div class="variant grid gap-3 rounded-2xl bg-white p-5 md:grid-cols-[1fr_1fr_1fr_auto]">
                        @if (isset($variant['id']))
                            <input
                                type="hidden"
                                name="variants[{{ $index }}][id]"
                                value="{{ $variant['id'] }}"
                            >
                        @endif

                        <label>
                            <span class="label">Nama</span>
                            <input
                                class="input"
                                name="variants[{{ $index }}][name]"
                                value="{{ $variant['name'] }}"
                                required
                            >
                        </label>

                        <label>
                            <span class="label">Harga</span>
                            <input
                                class="input"
                                type="number"
                                min="0"
                                name="variants[{{ $index }}][price]"
                                value="{{ $variant['price'] }}"
                                required
                            >
                        </label>

                        <label>
                            <span class="label">Stok</span>
                            <input
                                class="input"
                                type="number"
                                min="0"
                                name="variants[{{ $index }}][stock]"
                                value="{{ $variant['stock'] }}"
                                required
                            >
                        </label>

                        <button type="button" class="remove link-danger self-end px-3 py-2">
                            Hapus
                        </button>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end">
            <button class="btn-primary">Simpan produk</button>
        </div>
    </form>

    <template id="variant-template">
        <div class="variant grid gap-3 rounded-2xl bg-white p-5 md:grid-cols-[1fr_1fr_1fr_auto]">
            <label>
                <span class="label">Nama</span>
                <input class="input" data-field="name" required>
            </label>

            <label>
                <span class="label">Harga</span>
                <input class="input" type="number" min="0" data-field="price" required>
            </label>

            <label>
                <span class="label">Stok</span>
                <input class="input" type="number" min="0" data-field="stock" required>
            </label>

            <button type="button" class="remove link-danger self-end px-3 py-2">
                Hapus
            </button>
        </div>
    </template>

    <script>
        const variants = document.querySelector('#variants');
        const addVariantButton = document.querySelector('#add-variant');
        const variantTemplate = document.querySelector('#variant-template');

        addVariantButton.addEventListener('click', () => {
            const variant = variantTemplate.content.cloneNode(true);
            const index = Date.now();

            variant.querySelectorAll('[data-field]').forEach((field) => {
                field.name = `variants[${index}][${field.dataset.field}]`;
            });

            variants.append(variant);
        });

        variants.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove') && variants.children.length > 1) {
                event.target.closest('.variant').remove();
            }
        });
    </script>
@endsection
