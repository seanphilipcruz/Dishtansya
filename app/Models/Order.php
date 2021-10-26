<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity'
    ];

    public function Product() {
        return $this->belongsTo(Order::class);
    }

    public function User() {
        return $this->belongsToMany(User::class);
    }
}
