<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','product_id',
        'qty','quantity',
        'unit_price','price',
        'total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'price'      => 'decimal:2',
        'total'      => 'decimal:2',
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
