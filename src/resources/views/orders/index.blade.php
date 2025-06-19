@extends('layouts.app')

@section('title', '注文履歴')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">注文履歴</h1>
        <p class="text-gray-600 mt-2">過去のご注文一覧</p>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- 注文ヘッダー -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <p class="text-sm text-gray-600">注文番号</p>
                                    <p class="font-semibold text-gray-900">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">注文日</p>
                                    <p class="font-semibold text-gray-900">{{ $order->created_at->format('Y/m/d') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">合計金額</p>
                                    <p class="font-semibold text-blue-600">¥{{ number_format($order->total_amount) }}</p>
                                </div>
                            </div>
                            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                                <!-- ステータスバッジ -->
                                @switch($order->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⏳ 注文確認中
                                        </span>
                                        @break
                                    @case('paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            📦 準備中
                                        </span>
                                        @break
                                    @case('shipped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            🚚 配送中
                                        </span>
                                        @break
                                    @case('delivered')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ 配送完了
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ❌ キャンセル済み
                                        </span>
                                        @break
                                @endswitch
                                
                                <!-- アクションボタン -->
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    詳細を見る
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 注文商品 -->
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            @foreach($order->items->take(3) as $item)
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center mr-3">
                                        <span class="text-lg">
                                            @switch($item->product->category->slug)
                                                @case('smartphones')
                                                    📱
                                                    @break
                                                @case('computers')
                                                    💻
                                                    @break
                                                @case('mens-fashion')
                                                @case('womens-fashion')
                                                    👕
                                                    @break
                                                @case('books')
                                                    📚
                                                    @break
                                                @case('kitchen')
                                                    🍳
                                                    @break
                                                @case('sports-outdoors')
                                                    ⚽
                                                    @break
                                                @default
                                                    📦
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                        <p class="text-sm text-gray-600">数量: {{ $item->quantity }}個 × ¥{{ number_format($item->price) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">¥{{ number_format($item->getSubtotal()) }}</p>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($order->items->count() > 3)
                                <div class="text-center pt-2">
                                    <p class="text-sm text-gray-500">
                                        他{{ $order->items->count() - 3 }}件の商品
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- アクションエリア -->
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                @if($order->isCancellable())
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                onclick="return confirm('この注文をキャンセルしますか？')">
                                            キャンセル
                                        </button>
                                    </form>
                                @endif
                                
                                @if($order->status === 'delivered')
                                    <span class="text-green-600 text-sm">
                                        ✅ 商品をお受け取りいただき、ありがとうございました
                                    </span>
                                @endif
                            </div>
                            
                            <div class="text-right">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">
                                    注文詳細
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ページネーション -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <!-- 注文履歴がない場合 -->
        <div class="text-center py-16">
            <div class="text-6xl mb-4">📋</div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">注文履歴がありません</h2>
            <p class="text-gray-600 mb-8">まだご注文いただいた商品がございません。</p>
            <a href="{{ route('products.index') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 font-medium">
                🛍️ 商品を探す
            </a>
        </div>
    @endif
</div>
@endsection 