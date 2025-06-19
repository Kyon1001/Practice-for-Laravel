@extends('layouts.app')

@section('title', '注文詳細')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- ヘッダー -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">注文詳細</h1>
            <p class="text-gray-600 mt-1">注文番号: #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="text-right">
            <!-- ステータスバッジ -->
            @switch($order->status)
                @case('pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        ⏳ 注文確認中
                    </span>
                    @break
                @case('paid')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        📦 準備中
                    </span>
                    @break
                @case('shipped')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        🚚 配送中
                    </span>
                    @break
                @case('delivered')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        ✅ 配送完了
                    </span>
                    @break
                @case('cancelled')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        ❌ キャンセル済み
                    </span>
                    @break
            @endswitch
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 注文商品 -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">ご注文商品</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center py-4 border-b border-gray-200 last:border-b-0">
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
                                <h3 class="text-lg font-medium text-gray-900">
                                    <a href="{{ route('products.show', $item->product) }}" class="hover:text-blue-600">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
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
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-600 mr-8">商品合計:</span>
                                <span class="text-gray-900">¥{{ number_format($order->total_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600 mr-8">配送料:</span>
                                <span class="text-gray-900">無料</span>
                            </div>
                            <div class="flex justify-between items-center text-lg font-semibold pt-2 border-t border-gray-200">
                                <span class="text-gray-900 mr-8">合計:</span>
                                <span class="text-blue-600">¥{{ number_format($order->total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 配送進捗 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">配送状況</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">注文確定</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('Y年m月d日 H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ in_array($order->status, ['paid', 'shipped', 'delivered']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                @if(in_array($order->status, ['paid', 'shipped', 'delivered']))
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">商品準備完了</p>
                            <p class="text-sm text-gray-500">
                                @if(in_array($order->status, ['paid', 'shipped', 'delivered']))
                                    準備完了
                                @else
                                    準備中...
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">商品発送</p>
                            <p class="text-sm text-gray-500">
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    発送済み
                                @else
                                    発送準備中...
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $order->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                @if($order->status === 'delivered')
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">お届け完了</p>
                            <p class="text-sm text-gray-500">
                                @if($order->status === 'delivered')
                                    配送完了
                                @else
                                    配送中...
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 注文・配送情報 -->
        <div class="lg:col-span-1 space-y-6">
            <!-- 注文情報 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">注文情報</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">注文番号</p>
                        <p class="font-semibold text-gray-900">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">注文日時</p>
                        <p class="font-semibold text-gray-900">{{ $order->created_at->format('Y年m月d日 H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">お支払い方法</p>
                        <p class="font-semibold text-gray-900">💳 代金引換</p>
                    </div>
                </div>
            </div>

            <!-- 配送先情報 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">配送先</h2>
                
                <div class="bg-gray-50 rounded-md p-4">
                    <p class="whitespace-pre-line text-gray-900">{{ $order->shipping_address }}</p>
                    <p class="text-gray-700 mt-2">📞 {{ $order->shipping_phone }}</p>
                </div>
            </div>

            <!-- アクション -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">アクション</h2>
                
                <div class="space-y-3">
                    @if($order->isCancellable())
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium"
                                    onclick="return confirm('この注文をキャンセルしますか？')">
                                ❌ 注文をキャンセル
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" 
                       class="block w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 font-medium text-center">
                        📋 注文履歴に戻る
                    </a>
                    
                    <a href="{{ route('products.index') }}" 
                       class="block w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-medium text-center">
                        🛍️ 買い物を続ける
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 