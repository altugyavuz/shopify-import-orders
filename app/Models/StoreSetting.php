<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_alias',
        'store_name',
        'store_api_key',
    ];

    protected $guarded = [
        'store_api_key',
    ];
}
