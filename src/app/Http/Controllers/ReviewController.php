<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * レビュー投稿
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // 既にレビューを投稿しているかチェック
        $existingReview = Review::where('product_id', $product->id)
            ->where('buyer_id', $user->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'この商品には既にレビューを投稿しています。');
        }

        Review::create([
            'product_id' => $product->id,
            'buyer_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'レビューを投稿しました。');
    }

    /**
     * レビュー更新
     */
    public function update(Request $request, Review $review)
    {
        // 自分のレビューかチェック
        if ($review->buyer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'レビューを更新しました。');
    }

    /**
     * レビュー削除
     */
    public function destroy(Review $review)
    {
        // 自分のレビューかチェック
        if ($review->buyer_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'レビューを削除しました。');
    }
}
