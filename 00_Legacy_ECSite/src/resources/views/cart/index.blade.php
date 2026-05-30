@extends('layouts.app')

@section('title', 'ショッピングカート')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- ページヘッダー -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">ショッピングカート</h1>
        <p class="text-gray-600 mt-2">
            @if($cartItems->count() > 0)
                {{ $cartItems->sum('quantity') }}点の商品
            @else
                カートは空です
            @endif
        </p>
    </div>

    @if($cartItems->count() > 0)
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            <!-- カートアイテム一覧 -->
            <div class="lg:col-span-7">
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @foreach($cartItems as $item)
                            <div class="p-6 flex">
                                <!-- 商品画像 -->
                                <div class="flex-shrink-0">
                                    <div class="h-24 w-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">
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
                                </div>

                                <!-- 商品情報 -->
                                <div class="ml-6 flex-1">
                                    <div class="flex">
                                        <div class="flex-grow">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <a href="{{ route('products.show', $item->product) }}" 
                                                   class="hover:text-blue-600">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $item->product->category->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                出品者: {{ $item->product->seller->name }}
                                            </p>
                                        </div>

                                        <!-- 削除ボタン -->
                                        <div class="flex-shrink-0 ml-4">
                                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-400 hover:text-red-500 p-2"
                                                        onclick="return confirm('この商品をカートから削除しますか？')">
                                                    <span class="sr-only">削除</span>
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- 価格と数量 -->
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- 数量変更 -->
                                            <form action="{{ route('cart.update', $item) }}" method="POST" 
                                                  class="flex items-center space-x-2">
                                                @csrf
                                                @method('PATCH')
                                                <label for="quantity-{{ $item->id }}" class="text-sm text-gray-700">
                                                    数量:
                                                </label>
                                                <select name="quantity" id="quantity-{{ $item->id }}" 
                                                        onchange="this.form.submit()"
                                                        class="rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                    @for($i = 1; $i <= min($item->product->stock, 10); $i++)
                                                        <option value="{{ $i }}" 
                                                                {{ $item->quantity == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </form>

                                            <!-- 在庫状況 -->
                                            @if($item->product->stock <= 5)
                                                <span class="text-sm text-red-500">
                                                    残り{{ $item->product->stock }}個
                                                </span>
                                            @endif
                                        </div>

                                        <!-- 価格 -->
                                        <div class="text-right">
                                            <p class="text-lg font-medium text-gray-900">
                                                ¥{{ number_format($item->product->price * $item->quantity) }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                単価: ¥{{ number_format($item->product->price) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- カートアクション -->
                <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                    <a href="{{ route('products.index') }}" 
                       class="text-sm font-medium text-blue-600 hover:text-blue-500 flex items-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                        </svg>
                        買い物を続ける
                    </a>

                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-sm font-medium text-red-600 hover:text-red-500"
                                onclick="return confirm('カート内の全ての商品を削除しますか？')">
                            カートを空にする
                        </button>
                    </form>
                </div>
            </div>

            <!-- 注文サマリー -->
            <div class="lg:col-span-5 mt-16 lg:mt-0">
                <div class="bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">注文サマリー</h2>
                    
                    <div class="space-y-4">
                        <!-- 小計 -->
                        <div class="flex justify-between text-base text-gray-900">
                            <dt>小計</dt>
                            <dd>¥{{ number_format($totalAmount) }}</dd>
                        </div>
                        
                        <!-- 送料 -->
                        <div class="flex justify-between text-base text-gray-900">
                            <dt>送料</dt>
                            <dd>
                                @if($totalAmount >= 3000)
                                    <span class="text-green-600">無料</span>
                                @else
                                    ¥500
                                @endif
                            </dd>
                        </div>
                        
                        <!-- 合計 -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-lg font-medium text-gray-900">
                                <dt>合計</dt>
                                <dd>¥{{ number_format($totalAmount + ($totalAmount >= 3000 ? 0 : 500)) }}</dd>
                            </div>
                        </div>
                        
                        @if($totalAmount < 3000)
                            <p class="text-sm text-gray-500">
                                あと¥{{ number_format(3000 - $totalAmount) }}で送料無料
                            </p>
                        @endif
                    </div>

                    <!-- チェックアウトボタン -->
                    <div class="mt-6">
                        <a href="{{ route('orders.checkout') }}" 
                           class="w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center">
                            🛒 注文手続きへ進む
                        </a>
                    </div>

                    <!-- 安心・安全 -->
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="text-sm text-gray-600 space-y-2">
                            <div class="flex items-center">
                                <span class="mr-2">🔒</span>
                                <span>SSL暗号化で安全に決済</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-2">🚚</span>
                                <span>迅速・安全配送</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-2">↩️</span>
                                <span>30日間返品保証</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- 空のカート -->
        <div class="text-center py-16">
            <div class="text-8xl mb-4">🛒</div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">カートは空です</h2>
            <p class="text-gray-600 mb-8">まだ商品が追加されていません。</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                商品を探す
            </a>
        </div>
    @endif
</div>
@endsection 