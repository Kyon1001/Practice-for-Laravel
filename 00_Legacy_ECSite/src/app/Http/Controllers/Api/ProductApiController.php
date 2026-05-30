<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    /**
     * 商品一覧取得API
     */
    public function index(Request $request): JsonResponse
    {
        try {
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

            // キーワード検索
            if ($request->has('search') && $request->search) {
                $keyword = $request->search;
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
                });
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

            $perPage = min($request->get('per_page', 12), 50); // 最大50件まで
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'has_more' => $products->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '商品の取得に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * 商品詳細取得API
     */
    public function show(Product $product): JsonResponse
    {
        try {
            if ($product->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => '商品が見つかりません。'
                ], 404);
            }

            $product->load(['category', 'seller', 'images', 'reviews.buyer']);

            // 関連商品
            $relatedProducts = Product::with(['category', 'seller'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->limit(4)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'related_products' => $relatedProducts,
                    'average_rating' => $product->averageRating(),
                    'review_count' => $product->reviewCount(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '商品の取得に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * カテゴリー一覧取得API
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Category::whereNull('parent_id')
                ->with('children')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'カテゴリーの取得に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * 商品検索候補API
     */
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $keyword = $request->get('q');
            
            if (!$keyword || strlen($keyword) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $suggestions = Product::where('status', 'active')
                ->where('stock', '>', 0)
                ->where('name', 'LIKE', "%{$keyword}%")
                ->select('id', 'name')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '検索候補の取得に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
