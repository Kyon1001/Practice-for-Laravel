@extends('layouts.app')

@section('title', 'お気に入り')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">お気に入り</h1>
        <p class="text-gray-600 mt-2">お気に入りに登録した商品一覧</p>
    </div>

    @if($wishlists->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlists as $wishlist)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 relative">
                    <!-- お気に入り削除ボタン -->
                    <form action="{{ route('wishlist.remove', $wishlist->product) }}" method="POST" class="absolute top-2 right-2 z-10">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-white rounded-full p-1 shadow-md hover:bg-gray-50">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                    </form>

                    <a href="{{ route('products.show', $wishlist->product) }}">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-6xl">
                                @switch($wishlist->product->category->slug)
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
                                {{ $wishlist->product->name }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $wishlist->product->category->name }}</p>
                            
                            <!-- 評価 -->
                            @if($wishlist->product->reviewCount() > 0)
                                <div class="flex items-center mb-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $wishlist->product->averageRating() ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-1 text-xs text-gray-600">
                                        ({{ $wishlist->product->reviewCount() }})
                                    </span>
                                </div>
                            @endif
                            
                            <p class="text-xl font-bold text-blue-600">
                                ¥{{ number_format($wishlist->product->price) }}
                            </p>
                            
                            <!-- 在庫状況 -->
                            @if($wishlist->product->stock > 0)
                                <p class="text-sm text-green-600 mt-1">✅ 在庫あり</p>
                            @else
                                <p class="text-sm text-red-600 mt-1">❌ 在庫切れ</p>
                            @endif
                        </div>
                    </a>
                    
                    <!-- カートに追加ボタン -->
                    @if($wishlist->product->stock > 0)
                        <div class="p-4 pt-0">
                            <form action="{{ route('cart.add', $wishlist->product) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" 
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 text-sm">
                                    🛒 カートに追加
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- ページネーション -->
        <div class="mt-8">
            {{ $wishlists->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-6xl mb-4">💔</div>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">お気に入りがありません</h2>
            <p class="text-gray-600 mb-8">気になる商品をお気に入りに追加してみましょう</p>
            <a href="{{ route('products.index') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                商品を探す
            </a>
        </div>
    @endif
</div>
@endsection 