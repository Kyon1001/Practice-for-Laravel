<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartApiController extends Controller
{
    /**
     * カート内容取得API
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です。'
                ], 401);
            }
            
            $cart = Cart::with(['items.product.category', 'items.product.seller'])
                ->where('buyer_id', $user->id)
                ->first();
            
            if (!$cart) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'items' => [],
                        'total_amount' => 0,
                        'total_quantity' => 0
                    ]
                ]);
            }
            
            $totalAmount = $cart->items->reduce(function ($carry, $item) {
                return $carry + ($item->product->price * $item->quantity);
            }, 0);
            
            $totalQuantity = $cart->items->sum('quantity');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $cart->items,
                    'total_amount' => $totalAmount,
                    'total_quantity' => $totalQuantity
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'カート情報の取得に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * カートに商品追加API
     */
    public function add(Request $request, Product $product): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です。'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1|max:' . $product->stock,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'バリデーションエラー',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 商品が購入可能かチェック
            if ($product->status !== 'active' || $product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => '商品が購入できません。'
                ], 400);
            }

            // ログインユーザーのカートを取得（なければ作成）
            $cart = Cart::firstOrCreate(['buyer_id' => $user->id]);

            // 既にカートに同じ商品があるかチェック
            $existingCartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existingCartItem) {
                // 既存アイテムの数量を更新
                $newQuantity = $existingCartItem->quantity + $request->quantity;
                
                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => '在庫が不足しています。'
                    ], 400);
                }
                
                $existingCartItem->update(['quantity' => $newQuantity]);
                $cartItem = $existingCartItem;
            } else {
                // 新しいカートアイテムを作成
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                ]);
            }

            $cartItem->load('product');

            return response()->json([
                'success' => true,
                'message' => 'カートに商品を追加しました。',
                'data' => $cartItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'カートへの追加に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * カートアイテムの数量更新API
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です。'
                ], 401);
            }

            // カートの所有者確認
            if ($cartItem->cart->buyer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'アクセス権限がありません。'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'バリデーションエラー',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cartItem->update(['quantity' => $request->quantity]);
            $cartItem->load('product');

            return response()->json([
                'success' => true,
                'message' => '数量を更新しました。',
                'data' => $cartItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '数量の更新に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * カートから商品削除API
     */
    public function remove(CartItem $cartItem): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です。'
                ], 401);
            }

            // カートの所有者確認
            if ($cartItem->cart->buyer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'アクセス権限がありません。'
                ], 403);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'カートから商品を削除しました。'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'カートからの削除に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * カートを空にするAPI
     */
    public function clear(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '認証が必要です。'
                ], 401);
            }
            
            $cart = Cart::where('buyer_id', $user->id)->first();
            
            if ($cart) {
                CartItem::where('cart_id', $cart->id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'カートを空にしました。'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'カートのクリアに失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * カート内の商品数を取得API
     */
    public function count(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => true,
                    'data' => ['count' => 0]
                ]);
            }
            
            $cart = Cart::where('buyer_id', $user->id)->first();
            
            if (!$cart) {
                return response()->json([
                    'success' => true,
                    'data' => ['count' => 0]
                ]);
            }
            
            $count = CartItem::where('cart_id', $cart->id)->sum('quantity');
            
            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'カート数の取得に失敗しました。',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
