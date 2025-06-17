<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 商品一覧表示
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'seller'])
            ->where('status', 'active')
            ->where('stock', '>', 0);

        // カテゴリーでフィルタリング
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 価格範囲でフィルタリング
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // ソート
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * 商品詳細表示
     */
    public function show(Product $product)
    {
        // アクティブでない商品は表示しない
        if ($product->status !== 'active') {
            abort(404);
        }

        $product->load(['category', 'seller', 'images']);

        // 関連商品（同じカテゴリーの他の商品）
        $relatedProducts = Product::with(['category', 'seller'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * 商品検索
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q');
        
        if (!$keyword) {
            return redirect()->route('products.index');
        }

        $products = Product::with(['category', 'seller'])
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->latest()
            ->paginate(12)
            ->appends(['q' => $keyword]);

        $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('products.search', compact('products', 'categories', 'keyword'));
    }
}
