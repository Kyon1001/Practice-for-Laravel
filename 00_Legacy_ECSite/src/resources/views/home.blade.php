@extends('layouts.app')

@section('title', 'ホーム')

@section('content')
<!-- ヒーローセクション -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                最高の商品を<br>お得な価格で
            </h1>
            <p class="text-xl mb-8">
                数千種類の商品から、あなたにぴったりのアイテムを見つけてください
            </p>
            <a href="{{ route('products.index') }}" 
               class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                商品を見る
            </a>
        </div>
    </div>
</div>

<!-- カテゴリーセクション -->
<div class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-center mb-8">カテゴリーから探す</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 text-center">
            <div class="text-4xl mb-4">
                @switch($category->slug)
                    @case('electronics')
                        📱
                        @break
                    @case('fashion')
                        👕
                        @break
                    @case('books')
                        📚
                        @break
                    @case('home-kitchen')
                        🏠
                        @break
                    @case('sports-outdoors')
                        ⚽
                        @break
                    @default
                        🛍️
                @endswitch
            </div>
            <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
            <p class="text-sm text-gray-600 mt-2">{{ $category->children->count() }}個のサブカテゴリー</p>
        </a>
        @endforeach
    </div>
</div>

<!-- おすすめ商品セクション -->
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">おすすめ商品</h2>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                すべて見る →
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($newProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('products.show', $product) }}">
                    <!-- 商品画像プレースホルダー -->
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-6xl">
                            @switch($product->category->slug)
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
                    
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2 text-gray-800 line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $product->category->name }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-blue-600">
                                ¥{{ number_format($product->price) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                出品者: {{ $product->seller->name }}
                            </span>
                        </div>
                        @if($product->stock <= 5)
                            <p class="text-sm text-red-500 mt-2">残り{{ $product->stock }}個</p>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 特徴セクション -->
<div class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-center mb-8">なぜ私たちを選ぶのか</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
            <div class="text-5xl mb-4">🚚</div>
            <h3 class="text-xl font-semibold mb-2">迅速な配送</h3>
            <p class="text-gray-600">全国どこでも最短翌日お届け。お急ぎの商品も安心です。</p>
        </div>
        <div class="text-center">
            <div class="text-5xl mb-4">🔒</div>
            <h3 class="text-xl font-semibold mb-2">安全な取引</h3>
            <p class="text-gray-600">SSL暗号化通信により、お客様の個人情報を安全に保護します。</p>
        </div>
        <div class="text-center">
            <div class="text-5xl mb-4">💎</div>
            <h3 class="text-xl font-semibold mb-2">品質保証</h3>
            <p class="text-gray-600">厳選された商品のみを取り扱い、品質に自信を持っています。</p>
        </div>
    </div>
</div>
@endsection 