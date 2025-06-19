<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 注文一覧表示
     */
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['items.product', 'items.seller'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * 注文詳細表示
     */
    public function show(Order $order)
    {
        // 自分の注文かチェック
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.seller']);

        return view('orders.show', compact('order'));
    }

    /**
     * 注文確認画面表示
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'カートが空です。');
        }

        // カート商品の在庫チェック
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "「{$item->product->name}」の在庫が不足しています。");
            }
        }

        $cart->load(['items.product.seller']);
        $total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('orders.checkout', compact('cart', 'total'));
    }

    /**
     * 注文作成
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'shipping_phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'カートが空です。');
        }

        DB::beginTransaction();
        try {
            // 在庫チェック
            foreach ($cart->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', "「{$item->product->name}」の在庫が不足しています。");
                }
            }

            // 合計金額を計算
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            // 注文作成
            $order = Order::create([
                'buyer_id' => $user->id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'shipping_address' => $request->shipping_address,
                'shipping_phone' => $request->shipping_phone,
            ]);

            // 注文商品作成 & 在庫減算
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'seller_id' => $item->product->seller_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                // 在庫減算
                $item->product->decrement('stock', $item->quantity);
            }

            // カートクリア
            $cart->items()->delete();

            DB::commit();

            return redirect()->route('orders.complete', $order)
                ->with('success', '注文が完了しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', '注文処理でエラーが発生しました。')
                ->withInput();
        }
    }

    /**
     * 注文完了画面
     */
    public function complete(Order $order)
    {
        // 自分の注文かチェック
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.seller']);

        return view('orders.complete', compact('order'));
    }

    /**
     * 注文キャンセル
     */
    public function cancel(Order $order)
    {
        // 自分の注文かチェック
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        if (!$order->isCancellable()) {
            return redirect()->back()->with('error', 'この注文はキャンセルできません。');
        }

        DB::beginTransaction();
        try {
            // 在庫を戻す
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // 注文ステータス更新
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', '注文をキャンセルしました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'キャンセル処理でエラーが発生しました。');
        }
    }
}
