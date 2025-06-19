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
            <div class="flex justify-between items-start">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>
                
                <!-- お気に入りボタン -->
                @auth
                    @if($product->isWishlistedBy(auth()->id()))
                        <form action="{{ route('wishlist.remove', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('wishlist.add', $product) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            <div class="mt-3">
                <h2 class="sr-only">商品情報</h2>
                <p class="text-3xl text-blue-600 font-bold">¥{{ number_format($product->price) }}</p>
            </div>

            <!-- 評価 -->
            @if($product->reviewCount() > 0)
                <div class="mt-3 flex items-center">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $product->averageRating())
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="ml-2 text-sm text-gray-600">
                        {{ number_format($product->averageRating(), 1) }} ({{ $product->reviewCount() }}件のレビュー)
                    </span>
                </div>
            @endif

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

    <!-- レビューセクション -->
    <div class="mt-16">
        <div class="border-t border-gray-200 pt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">レビュー</h2>
            
            @auth
                @if(!$userReview)
                    <!-- レビュー投稿フォーム -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">レビューを投稿</h3>
                        <form action="{{ route('reviews.store', $product) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">評価</label>
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" class="sr-only" required>
                                        <label for="rating{{ $i }}" class="cursor-pointer">
                                            <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 fill-current rating-star" data-rating="{{ $i }}" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">コメント</label>
                                <textarea id="comment" name="comment" rows="3" 
                                          class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="商品の感想をお聞かせください"></textarea>
                            </div>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                レビューを投稿
                            </button>
                        </form>
                    </div>
                @else
                    <!-- 既存レビューの表示・編集 -->
                    <div class="bg-blue-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">あなたのレビュー</h3>
                        <div class="flex items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($userReview->comment)
                            <p class="text-gray-700 mb-4">{{ $userReview->comment }}</p>
                        @endif
                        <div class="flex space-x-2">
                            <button onclick="editReview({{ $userReview->id }})" 
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                編集
                            </button>
                            <form action="{{ route('reviews.destroy', $userReview) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 text-sm"
                                        onclick="return confirm('レビューを削除しますか？')">
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- レビュー一覧 -->
            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ $review->buyer->name }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('Y年m月d日') }}</span>
                            </div>
                            @if($review->comment)
                                <p class="text-gray-700">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- ページネーション -->
                <div class="mt-8">
                    {{ $reviews->links() }}
                </div>
            @else
                <p class="text-gray-500 text-center py-8">まだレビューがありません。</p>
            @endif
        </div>
    </div>

    <!-- 関連商品 -->
    @if($relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">関連商品</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                <div class="group relative">
                    <div class="w-full min-h-80 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
                        <div class="w-full h-full flex items-center justify-center">
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
                    </div>
                    <div class="mt-4 flex justify-between">
                        <div>
                            <h3 class="text-sm text-gray-700">
                                <a href="{{ route('products.show', $relatedProduct) }}">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    {{ Str::limit($relatedProduct->name, 50) }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $relatedProduct->category->name }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">¥{{ number_format($relatedProduct->price) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
// 星評価のJavaScript
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-star');
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            if (ratingInputs[rating - 1]) {
                ratingInputs[rating - 1].checked = true;
            }
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            // 選択済みの評価を復元
            const checkedRating = document.querySelector('input[name="rating"]:checked');
            const selectedRating = checkedRating ? parseInt(checkedRating.value) : 0;
            
            stars.forEach((s, index) => {
                if (index < selectedRating) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
    });
});

// レビュー編集機能（簡易版）
function editReview(reviewId) {
    if (confirm('レビューを編集しますか？')) {
        alert('レビュー編集機能は今後実装予定です。（レビューID: ' + reviewId + '）');
    }
}
</script>
@endsection 