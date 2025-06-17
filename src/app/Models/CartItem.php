<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * カート
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * 商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 小計（商品価格×数量）
     */
    public function getSubtotal()
    {
        return $this->quantity * $this->product->price;
    }
}
