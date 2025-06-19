<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * カート画面表示
     */
    public function index()
    {
        $user = Auth::user();
        
        // ログインユーザーのカートを取得（なければ作成）
        $cart = Cart::firstOrCreate(['buyer_id' => $user->id]);
        
        // カートアイテムを商品情報と一緒に取得
        $cartItems = CartItem::with(['product.category', 'product.seller'])
            ->where('cart_id', $cart->id)
            ->get();
        
        // 合計金額計算
        $totalAmount = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->product->price * $item->quantity);
        }, 0);
        
        return view('cart.index', compact('cartItems', 'totalAmount'));
    }

    /**
     * カートに商品追加
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        $user = Auth::user();
        
        // 商品が購入可能かチェック
        if ($product->status !== 'active' || $product->stock < $request->quantity) {
            return redirect()->back()->with('error', '商品が購入できません。');
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
                return redirect()->back()->with('error', '在庫が不足しています。');
            }
            
            $existingCartItem->update(['quantity' => $newQuantity]);
        } else {
            // 新しいカートアイテムを作成
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'カートに商品を追加しました。');
    }

    /**
     * カートアイテムの数量更新
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock,
        ]);

        // カートの所有者確認
        if ($cartItem->cart->buyer_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', '数量を更新しました。');
    }

    /**
     * カートから商品削除
     */
    public function remove(CartItem $cartItem)
    {
        // カートの所有者確認
        if ($cartItem->cart->buyer_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'カートから商品を削除しました。');
    }

    /**
     * カートを空にする
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = Cart::where('buyer_id', $user->id)->first();
        
        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
        }

        return redirect()->route('cart.index')->with('success', 'カートを空にしました。');
    }

    /**
     * カート内の商品数を取得（AJAX用）
     */
    public function count()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $cart = Cart::where('buyer_id', $user->id)->first();
        
        if (!$cart) {
            return response()->json(['count' => 0]);
        }
        
        $count = CartItem::where('cart_id', $cart->id)->sum('quantity');
        
        return response()->json(['count' => $count]);
    }
} 