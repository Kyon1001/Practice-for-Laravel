<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'ECサイト')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Custom Styles -->
        <style>
            .cart-count {
                font-size: 0.75rem;
                min-width: 1.25rem;
                height: 1.25rem;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- ナビゲーション -->
            <nav class="bg-white shadow-lg sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex justify-between items-center h-16">
                        <!-- ロゴ -->
                        <div class="flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                                🛒 ECサイト
                            </a>
                        </div>

                        <!-- 検索バー -->
                        <div class="flex-1 max-w-lg mx-8">
                            <form action="{{ route('products.search') }}" method="GET" class="flex">
                                <input type="text" name="q" placeholder="商品を検索..." 
                                       value="{{ request('q') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    検索
                                </button>
                            </form>
                        </div>

                        <!-- ユーザーメニュー -->
                        <div class="flex items-center space-x-4">
                            @auth
                                <!-- カート -->
                                <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-gray-900 relative">
                                    🛒 <span class="text-sm">カート</span>
                                    <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" style="display: none;">
                                        0
                                    </span>
                                </a>
                                
                                <!-- ダッシュボード -->
                                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                                    ダッシュボード
                                </a>
                                
                                <!-- ログアウト -->
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900">
                                        ログアウト
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">ログイン</a>
                                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    新規登録
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- カテゴリーナビゲーション -->
            @if(isset($categories) && $categories->count() > 0)
            <div class="bg-gray-50 border-b">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex space-x-8 py-3">
                        <a href="{{ route('products.index') }}" 
                           class="text-sm text-gray-600 hover:text-gray-900">すべての商品</a>
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                               class="text-sm text-gray-600 hover:text-gray-900">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- メインコンテンツ -->
            <main class="pt-4">
                <!-- フラッシュメッセージ -->
                @if(session('success'))
                    <div class="max-w-7xl mx-auto px-4 pb-4">
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="max-w-7xl mx-auto px-4 pb-4">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- フッター -->
            <footer class="bg-gray-800 text-white mt-12">
                <div class="max-w-7xl mx-auto px-4 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">ECサイト</h3>
                            <p class="text-gray-300">
                                便利で安全なオンラインショッピング体験を提供します。
                            </p>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold mb-4">カテゴリー</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="#" class="hover:text-white">家電・電子機器</a></li>
                                <li><a href="#" class="hover:text-white">ファッション</a></li>
                                <li><a href="#" class="hover:text-white">本・雑誌</a></li>
                                <li><a href="#" class="hover:text-white">ホーム・キッチン</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold mb-4">サポート</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="#" class="hover:text-white">お問い合わせ</a></li>
                                <li><a href="#" class="hover:text-white">配送について</a></li>
                                <li><a href="#" class="hover:text-white">返品について</a></li>
                                <li><a href="#" class="hover:text-white">よくある質問</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-md font-semibold mb-4">アカウント</h4>
                            <ul class="space-y-2 text-gray-300">
                                @auth
                                    <li><a href="{{ route('dashboard') }}" class="hover:text-white">マイページ</a></li>
                                    <li><a href="#" class="hover:text-white">注文履歴</a></li>
                                @else
                                    <li><a href="{{ route('login') }}" class="hover:text-white">ログイン</a></li>
                                    <li><a href="{{ route('register') }}" class="hover:text-white">新規登録</a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                        <p>&copy; {{ date('Y') }} ECサイト. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>

        <!-- JavaScript -->
        <script>
            // CSRF Token Setup
            window.csrfToken = '{{ csrf_token() }}';
            
            // Cart functionality
            async function addToCart(productId, quantity = 1) {
                try {
                    const response = await fetch('/api/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        updateCartCount(data.cart_count);
                        showSuccess('商品をカートに追加しました');
                    } else {
                        showError(data.message || 'エラーが発生しました');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError('ネットワークエラーが発生しました');
                }
            }

            function updateCartCount(count) {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = count;
                    cartCount.style.display = count > 0 ? 'flex' : 'none';
                }
            }

            function showSuccess(message) {
                // Simple alert for now - can be enhanced later
                alert(message);
            }

            function showError(message) {
                alert(message);
            }

            // Initialize cart count on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Fetch current cart count
                fetch('/api/cart/count', {
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartCount(data.count);
                    }
                })
                .catch(error => console.error('Error loading cart count:', error));
            });
        </script>
    </body>
</html>
