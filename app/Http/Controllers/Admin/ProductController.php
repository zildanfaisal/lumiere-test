<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', ['products' => Product::with('variants')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product]);
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product->load('variants')]);
    }

    public function store(ProductRequest $request)
    {
        $this->save(new Product, $request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->save($product, $request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    private function save(Product $product, array $data): void
    {
        DB::transaction(function () use ($product, $data) {
            $product->fill(['name' => $data['name'], 'description' => $data['description'] ?? null])->save();
            $keep = [];
            foreach ($data['variants'] as $variant) {
                $model = isset($variant['id']) ? $product->variants()->find($variant['id']) : null;
                $model ??= $product->variants()->make();
                $model->fill($variant)->save();
                $keep[] = $model->id;
            }
            $product->variants()->whereNotIn('id', $keep)->delete();
        });
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
        } catch (QueryException) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah tercatat dalam pesanan.');
        }

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
