@extends('layouts.app')

@section('title', '検索結果: ' . $keyword)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- 検索結果ヘッダー -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">検索結果</h1>
        <p class="text-gray-600 mt-2">
            「<span class="font-semibold text-blue-600">{{ $keyword }}</span>」の検索結果: {{ $products->total() }}件
        </p>
    </div>

    <!-- 検索フォーム -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('products.search') }}" method="GET" class="flex">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="商品を検索..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 transition duration-300">
                検索
            </button>
        </form>
    </div>

    <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- サイドバー（フィルター） -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">絞り込み</h3>
                
                <form method="GET" action="{{ route('products.search') }}">
                    <input type="hidden" name="q" value="{{ $keyword }}">
                    
                    <!-- カテゴリーフィルター -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">カテゴリー</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="category" value="" 
                                       {{ !request('category') ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm">すべて</span>
                            </label>
                            @foreach($categories as $category)
                                <label class="flex items-center">
                                    <input type="radio" name="category" value="{{ $category->slug }}" 
                                           {{ request('category') == $category->slug ? 'checked' : '' }}
                                           class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm">{{ $category->name }}</span>
                                </label>
                                @foreach($category->children as $child)
                                    <label class="flex items-center ml-4">
                                        <input type="radio" name="category" value="{{ $child->slug }}" 
                                               {{ request('category') == $child->slug ? 'checked' : '' }}
                                               class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm">{{ $child->name }}</span>
                                    </label>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- 価格フィルター -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">価格範囲</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm text-gray-700">最低価格</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="0" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">最高価格</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="999999" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">
                        フィルターを適用
                    </button>
                </form>

                <!-- 検索のヒント -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">検索のコツ</h4>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• 商品名の一部を入力</li>
                        <li>• 複数の単語で検索</li>
                        <li>• カテゴリーで絞り込み</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- メインコンテンツ -->
        <div class="lg:col-span-3 mt-8 lg:mt-0">
            <!-- 検索結果 -->
            @if($products->count() > 0)
                <!-- ソート -->
                <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">並び順:</span>
                            <form method="GET" action="{{ route('products.search') }}" class="flex items-center space-x-2">
                                <input type="hidden" name="q" value="{{ $keyword }}">
                                @if(request('category'))
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                @endif
                                @if(request('min_price'))
                                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                @endif
                                @if(request('max_price'))
                                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                @endif
                                
                                <select name="sort" onchange="this.form.submit()" 
                                        class="text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>新着順</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>価格の安い順</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>価格の高い順</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>名前順</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 商品グリッド -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            <a href="{{ route('products.show', $product) }}">
                                <!-- 商品画像 -->
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
                                        {!! str_ireplace($keyword, '<mark class="bg-yellow-200">'.$keyword.'</mark>', $product->name) !!}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $product->category->name }}</p>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {!! str_ireplace($keyword, '<mark class="bg-yellow-200">'.$keyword.'</mark>', Str::limit($product->description, 80)) !!}
                                    </p>
                                    
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xl font-bold text-blue-600">
                                            ¥{{ number_format($product->price) }}
                                        </span>
                                        @if($product->stock <= 5)
                                            <span class="text-sm text-red-500">残り{{ $product->stock }}個</span>
                                        @else
                                            <span class="text-sm text-green-600">在庫あり</span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-sm text-gray-500">
                                        出品者: {{ $product->seller->name }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- ページネーション -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">検索結果が見つかりませんでした</h3>
                    <p class="text-gray-600 mb-4">
                        「{{ $keyword }}」に関する商品は見つかりませんでした。<br>
                        別のキーワードで検索してみてください。
                    </p>
                    <div class="space-y-2">
                        <a href="{{ route('products.index') }}" 
                           class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 mr-2">
                            すべての商品を見る
                        </a>
                        <button onclick="document.querySelector('input[name=q]').focus()" 
                                class="inline-block bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                            再検索
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 