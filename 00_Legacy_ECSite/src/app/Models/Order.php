<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'status',
        'total_amount',
        'shipping_address',
        'shipping_phone',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * 購入者
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 注文商品
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 注文が完了しているかどうか
     */
    public function isCompleted()
    {
        return in_array($this->status, ['delivered']);
    }

    /**
     * 注文がキャンセル可能かどうか
     */
    public function isCancellable()
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    /**
     * 配送可能かどうか
     */
    public function isShippable()
    {
        return $this->status === 'paid';
    }
}
