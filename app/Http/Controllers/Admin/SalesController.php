<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $sales = Order::query()
            ->select(['id', 'user_id', 'status', 'total', 'paid_at'])
            ->with([
                'user:id,name,email',
                'items:id,order_id,product_name,variant_name,unit_price,quantity,subtotal',
            ])
            ->where('status', Order::PAID)
            ->orderByDesc('paid_at')
            ->paginate(15);

        $summary = Order::query()
            ->where('status', Order::PAID)
            ->selectRaw('COUNT(*) as order_count, COALESCE(SUM(total), 0) as revenue')
            ->firstOrFail();

        return view('admin.sales.index', [
            'sales' => $sales,
            'orderCount' => (int) $summary->order_count,
            'revenue' => $summary->revenue,
        ]);
    }
}
