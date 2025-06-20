<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    /**
     * 親カテゴリ
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * 子カテゴリ
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * このカテゴリの商品
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * ルートカテゴリかどうか
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }
}
