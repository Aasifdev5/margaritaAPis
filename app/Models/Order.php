<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'total',
        'status',
        'payment_mode',
        'payment_status',
        'items',
        'delivery_address',
        'order_type',
        'notes',
    ];

    protected $casts = [
        'items' => 'array', // Automatically decode JSON to array
        'status' => 'string',
        'payment_mode' => 'string',
        'payment_status' => 'string',
        'order_type' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Generate unique order number
    public static function generateOrderNumber()
    {
        $year = date('Y');
        $prefix = "ORD-$year-";
        $lastOrder = self::where('order_number', 'like', "$prefix%")
            ->orderBy('order_number', 'desc')
            ->first();
        $number = $lastOrder ? (int) substr($lastOrder->order_number, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
