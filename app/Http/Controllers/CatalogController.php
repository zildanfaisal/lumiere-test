<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        return view('catalog.index', ['products' => Product::with('variants')->latest()->get()]);
    }
}
