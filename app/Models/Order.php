<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const PENDING = 'Menunggu Pembayaran';

    public const PAID = 'Dibayar';

    public const CANCELLED = 'Dibatalkan';

    protected $fillable = ['user_id', 'status', 'total', 'paid_at', 'cancelled_at'];

    protected function casts(): array
    {
        return ['total' => 'decimal:2', 'paid_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
