<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * 注文
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 出品者
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * 小計（価格×数量）
     */
    public function getSubtotal()
    {
        return $this->quantity * $this->price;
    }
}
