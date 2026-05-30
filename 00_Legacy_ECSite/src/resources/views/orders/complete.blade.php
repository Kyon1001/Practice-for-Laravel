@extends('layouts.app')

@section('title', '注文完了')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- 成功メッセージ -->
    <div class="text-center mb-8">
        <div class="text-6xl mb-4">🎉</div>
        <h1 class="text-3xl font-bold text-green-600 mb-2">注文が完了しました！</h1>
        <p class="text-gray-600">ご注文いただき、ありがとうございます。</p>
    </div>

    <!-- 注文情報 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-900">注文情報</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">注文番号</p>
                <p class="text-lg font-semibold text-gray-900">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">注文日時</p>
                <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('Y年m月d日 H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">ご注文金額</p>
                <p class="text-lg font-semibold text-blue-600">¥{{ number_format($order->total_amount) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">お支払い方法</p>
                <p class="text-lg font-semibold text-gray-900">💳 代金引換</p>
            </div>
        </div>

        <!-- 配送先情報 -->
        <div class="border-t border-gray-200 pt-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">配送先</h3>
            <div class="bg-gray-50 rounded-md p-4">
                <p class="whitespace-pre-line text-gray-900">{{ $order->shipping_address }}</p>
                <p class="text-gray-700 mt-2">📞 {{ $order->shipping_phone }}</p>
            </div>
        </div>
    </div>

    <!-- 注文商品 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">ご注文商品</h2>
        
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center py-4 border-b border-gray-200">
                    <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center mr-4">
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
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">{{ $item->product->name }}</h3>
                        <p class="text-gray-600">出品者: {{ $item->seller->name }}</p>
                        <p class="text-sm text-gray-500">数量: {{ $item->quantity }}個</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-gray-900">
                            ¥{{ number_format($item->getSubtotal()) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            ¥{{ number_format($item->price) }} × {{ $item->quantity }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-200 pt-4 mt-4">
            <div class="flex justify-end">
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900">
                        合計: <span class="text-blue-600">¥{{ number_format($order->total_amount) }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 次のステップ -->
    <div class="bg-blue-50 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">今後の流れ</h3>
        <div class="space-y-3">
            <div class="flex items-center">
                <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">1</span>
                <span class="text-blue-900">出品者が商品を準備します</span>
            </div>
            <div class="flex items-center">
                <span class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-semibold mr-3">2</span>
                <span class="text-gray-700">商品が発送されます</span>
            </div>
            <div class="flex items-center">
                <span class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-semibold mr-3">3</span>
                <span class="text-gray-700">商品をお受け取りください</span>
            </div>
        </div>
        <p class="text-sm text-blue-700 mt-4">
            配送状況は「注文履歴」からご確認いただけます。
        </p>
    </div>

    <!-- アクションボタン -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('orders.index') }}" 
           class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 text-center font-medium">
            📋 注文履歴を見る
        </a>
        <a href="{{ route('products.index') }}" 
           class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 text-center font-medium">
            🛍️ 買い物を続ける
        </a>
        <a href="{{ route('home') }}" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-400 text-center font-medium">
            🏠 ホームに戻る
        </a>
    </div>

    <!-- 重要なお知らせ -->
    <div class="mt-8 bg-amber-50 border-l-4 border-amber-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="text-amber-400 text-xl">💡</span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-amber-800">重要なお知らせ</h3>
                <div class="mt-2 text-sm text-amber-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>商品は代金引換でお届けします</li>
                        <li>配送料は無料です</li>
                        <li>キャンセルをご希望の場合は、商品発送前までに注文履歴からお手続きください</li>
                        <li>ご不明な点がございましたら、出品者にお問い合わせください</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 