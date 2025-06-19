<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * お気に入り一覧表示
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()
            ->with(['product.category', 'product.seller'])
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * お気に入りに追加
     */
    public function store(Product $product)
    {
        $user = Auth::user();

        // 既にお気に入りに追加されているかチェック
        if ($product->isWishlistedBy($user->id)) {
            return back()->with('info', 'この商品は既にお気に入りに追加されています。');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'お気に入りに追加しました。');
    }

    /**
     * お気に入りから削除
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'お気に入りから削除しました。');
        }

        return back()->with('error', 'お気に入りが見つかりません。');
    }
}
