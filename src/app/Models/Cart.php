<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
    ];

    /**
     * 購入者
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * カート商品
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * カートの合計金額
     */
    public function getTotalAmount()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * カートのアイテム数
     */
    public function getTotalItems()
    {
        return $this->items->sum('quantity');
    }
}