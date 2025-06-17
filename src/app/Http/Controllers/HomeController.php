<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * トップページ表示
     */
    public function index()
    {
        // アクティブな商品を最新順で8件取得
        $featuredProducts = Product::with(['category', 'seller'])
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->latest()
            ->limit(8)
            ->get();

        // メインカテゴリー（親カテゴリー）を取得
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }
}
