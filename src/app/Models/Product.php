<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * 出品者
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * カテゴリ
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 商品画像
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * メイン画像
     */
    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * カート商品
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * 注文商品
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 在庫があるかどうか
     */
    public function hasStock()
    {
        return $this->stock > 0;
    }

    /**
     * アクティブかどうか
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
