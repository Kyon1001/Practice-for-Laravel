@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- パンくずナビ -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">
                    ホーム
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-blue-600">
                        商品一覧
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" 
                       class="text-gray-700 hover:text-blue-600">
                        {{ $product->category->name }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-gray-500">{{ Str::limit($product->name, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
        <!-- 商品画像 -->
        <div class="flex flex-col-reverse">
            <div class="w-full aspect-w-1 aspect-h-1 lg:aspect-none lg:h-80">
                <div class="h-80 w-full bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-8xl">
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
            </div>
        </div>

        <!-- 商品情報 -->
        <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>

            <div class="mt-3">
                <h2 class="sr-only">商品情報</h2>
                <p class="text-3xl text-blue-600 font-bold">¥{{ number_format($product->price) }}</p>
            </div>

            <!-- カテゴリー -->
            <div class="mt-6">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $product->category->name }}
                </span>
            </div>

            <!-- 説明 -->
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900">商品説明</h3>
                <div class="mt-4 prose prose-sm text-gray-600">
                    <p>{{ $product->description }}</p>
                </div>
            </div>

            <!-- 在庫状況 -->
            <div class="mt-6">
                @if($product->stock > 0)
                    @if($product->stock <= 5)
                        <p class="text-sm text-red-600">
                            ⚠️ 残り{{ $product->stock }}個
                        </p>
                    @else
                        <p class="text-sm text-green-600">
                            ✅ 在庫あり
                        </p>
                    @endif
                @else
                    <p class="text-sm text-red-600">
                        ❌ 在庫切れ
                    </p>
                @endif
            </div>

            <!-- 出品者情報 -->
            <div class="mt-6 border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-gray-900">出品者情報</h3>
                <div class="mt-4 flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ substr($product->seller->name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $product->seller->name }}</p>
                        <p class="text-sm text-gray-500">
                            @if($product->seller->isSeller())
                                認定出品者
                            @else
                                一般出品者
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- カートボタン -->
            <div class="mt-10">
                @auth
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <div class="flex items-center space-x-4 mb-4">
                                <label for="quantity" class="text-sm font-medium text-gray-700">数量:</label>
                                <select id="quantity" name="quantity" 
                                        class="rounded-md border-gray-300 text-base focus:border-blue-500 focus:ring-blue-500">
                                    @for($i = 1; $i <= min($product->stock, 10); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-blue-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                🛒 カートに追加
                            </button>
                        </form>
                    @else
                        <button disabled 
                                class="w-full bg-gray-400 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white cursor-not-allowed">
                            在庫切れ
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" 
                       class="w-full bg-blue-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-blue-700">
                        ログインして購入
                    </a>
                @endauth
            </div>

            <!-- 安心・安全 -->
            <div class="mt-6 border-t border-gray-200 pt-6">
                <div class="flex items-center space-x-6 text-sm text-gray-600">
                    <div class="flex items-center">
                        <span class="mr-2">🔒</span>
                        <span>安全な決済</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">🚚</span>
                        <span>迅速配送</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">↩️</span>
                        <span>返品保証</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 関連商品 -->
    @if($relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">関連商品</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('products.show', $relatedProduct) }}">
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-6xl">
                            @switch($relatedProduct->category->slug)
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
                            {{ $relatedProduct->name }}
                        </h3>
                        <p class="text-xl font-bold text-blue-600">
                            ¥{{ number_format($relatedProduct->price) }}
                        </p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection 