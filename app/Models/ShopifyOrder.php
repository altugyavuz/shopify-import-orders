<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class ShopifyOrder extends Model
{
    protected $fillable = [
        'shopify_order_id',
        'customer_email',
        'total_price',
        'status',
        'ordered_at',
    ];

    protected $casts = [
        'ordered_at' => 'datetime:d-m-Y H:i:s',
        'status'     => OrderStatus::class,
    ];

    public $dates = [
        'ordered_at',
    ];
}
