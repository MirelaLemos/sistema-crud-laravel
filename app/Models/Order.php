<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'status',
        'total_cents',
        'stripe_session_id',
        'customer_email',
        'customer_name',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Se você criou o model Payment e a tabela payments, pode manter:
    // public function payment() { return $this->hasOne(Payment::class); }
}
