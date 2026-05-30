<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * ホームページ表示
     */
    public function index()
    {
        // 人気商品（キャッシュ使用）
        $popularProducts = Cache::remember('popular_products', 3600, function () {
            return Product::with(['category', 'seller'])
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->withCount('reviews')
                ->orderBy('reviews_count', 'desc')
                ->limit(8)
                ->get();
        });

        // 新着商品（キャッシュ使用）
        $newProducts = Cache::remember('new_products', 1800, function () {
            return Product::with(['category', 'seller'])
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->latest()
                ->limit(8)
                ->get();
        });

        // カテゴリー（キャッシュ使用）
        $categories = Cache::remember('main_categories', 3600, function () {
            return Category::whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->limit(5);
                }])
                ->limit(6)
                ->get();
        });

        // おすすめ商品（価格帯別）
        $recommendedProducts = Cache::remember('recommended_products', 3600, function () {
            return Product::with(['category', 'seller'])
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->whereBetween('price', [1000, 50000])
                ->inRandomOrder()
                ->limit(4)
                ->get();
        });

        return view('home', compact('popularProducts', 'newProducts', 'categories', 'recommendedProducts'));
    }
}
