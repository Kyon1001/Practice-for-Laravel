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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- ナビゲーション -->
            <nav class="bg-white shadow-lg">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex justify-between h-16">
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
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                                    検索
                                </button>
                            </form>
                        </div>

                        <!-- ユーザーメニュー -->
                        <div class="flex items-center space-x-4">
                            @auth
                                <!-- カート -->
                                <a href="#" class="text-gray-600 hover:text-gray-900">
                                    🛒 <span class="text-sm">カート</span>
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
            <main>
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
    </body>
</html>
