<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'items',
        'delivery_address',
        'delivery_option',
        'payment_status',
        'total_amount',
        'order_status',
    ];

    // Cast the items field as an array
    protected $casts = [
        'items' => 'array',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
