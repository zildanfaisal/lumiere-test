<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['product_variant_id', 'product_name', 'variant_name', 'unit_price', 'quantity', 'subtotal'];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
