<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','product_id','name','unit_price_cents','qty','line_total_cents'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helpers se usou *_cents
    public function getUnitPriceAttribute()
    {
        return ($this->unit_price_cents ?? 0) / 100;
    }

    public function getLineTotalAttribute()
    {
        return ($this->line_total_cents ?? 0) / 100;
    }
}
