<?php

namespace App\Http\Controllers;

use App\Models\ShopifyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $orderCount = Cache::remember('orderCount', 60 * 5, function () {
            return ShopifyOrder::count();
        });

        return Inertia::render('Dashboard', [
            'orderCount' => $orderCount,
        ]);
    }
}
