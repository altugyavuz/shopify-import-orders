<?php

namespace App\Http\Controllers;

use App\Jobs\PrepareOrderImportJob;
use App\Models\ShopifyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class OrderController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $importingProcess = Redis::get('shopify_orders_importing');

        $orders = ShopifyOrder::query()
            ->orderByDesc('ordered_at')
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders'           => $orders,
            'importingProcess' => $importingProcess == 1,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importOrders(Request $request)
    {
        ShopifyOrder::truncate();

        Redis::set('shopify_orders_importing', 1);

        dispatch(new PrepareOrderImportJob());

        return back(303);
    }
}
