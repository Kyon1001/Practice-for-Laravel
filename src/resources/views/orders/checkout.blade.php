@extends('layouts.app')

@section('title', '注文確認')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">注文確認</h1>
        <p class="text-gray-600 mt-2">ご注文内容をご確認ください</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 注文内容 -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">注文商品</h2>
                
                <div class="space-y-4">
                    @foreach($cart->items as $item)
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
                                <p class="text-gray-600">出品者: {{ $item->product->seller->name }}</p>
                                <p class="text-sm text-gray-500">数量: {{ $item->quantity }}個</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">
                                    ¥{{ number_format($item->product->price * $item->quantity) }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    ¥{{ number_format($item->product->price) }} × {{ $item->quantity }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 注文フォーム -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">配送先情報</h2>
                
                <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">
                            配送先住所 <span class="text-red-500">*</span>
                        </label>
                        <textarea id="shipping_address" 
                                  name="shipping_address" 
                                  rows="3" 
                                  class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="〒000-0000&#10;東京都渋谷区○○1-2-3&#10;○○マンション101号室"
                                  required>{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            電話番号 <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               id="shipping_phone" 
                               name="shipping_phone" 
                               class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                               placeholder="090-0000-0000"
                               value="{{ old('shipping_phone') }}"
                               required>
                        @error('shipping_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 決済方法 -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">決済方法</h3>
                        <div class="bg-gray-50 rounded-md p-3">
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">💳</span>
                                <span class="text-gray-900">代金引換</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                商品お届け時にお支払いください
                            </p>
                        </div>
                    </div>

                    <!-- 注文金額 -->
                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">商品合計</span>
                            <span class="text-gray-900">¥{{ number_format($total) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">配送料</span>
                            <span class="text-gray-900">無料</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-semibold pt-2 border-t border-gray-200">
                            <span class="text-gray-900">合計</span>
                            <span class="text-blue-600">¥{{ number_format($total) }}</span>
                        </div>
                    </div>

                    <!-- 注文ボタン -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 font-medium transition duration-150"
                            onclick="return confirm('注文を確定しますか？')">
                        🛒 注文を確定する
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('cart.index') }}" 
                       class="text-gray-600 hover:text-gray-800 text-sm">
                        ← カートに戻る
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 注意事項 -->
    <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="text-yellow-400 text-xl">⚠️</span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">ご注文前にご確認ください</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>注文確定後のキャンセルは商品発送前のみ可能です</li>
                        <li>配送先住所に間違いがないかご確認ください</li>
                        <li>商品の在庫状況により、発送が遅れる場合があります</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// フォーム送信時の二重送信防止
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = '処理中...';
    
    // 3秒後にボタンを再有効化（エラー時のため）
    setTimeout(() => {
        button.disabled = false;
        button.innerHTML = '🛒 注文を確定する';
    }, 3000);
});
</script>
@endsection 