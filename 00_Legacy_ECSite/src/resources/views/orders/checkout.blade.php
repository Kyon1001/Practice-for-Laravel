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
                    
                    <!-- 郵便番号 -->
                    <div class="mb-4">
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            郵便番号 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="postal_code" 
                               name="postal_code" 
                               class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                               placeholder="123-4567"
                               pattern="[0-9]{3}-[0-9]{4}"
                               value="{{ old('postal_code') }}"
                               required>
                        @error('postal_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 都道府県 -->
                    <div class="mb-4">
                        <label for="prefecture" class="block text-sm font-medium text-gray-700 mb-2">
                            都道府県 <span class="text-red-500">*</span>
                        </label>
                        <select id="prefecture" 
                                name="prefecture" 
                                class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                                required>
                            <option value="">選択してください</option>
                            <option value="北海道" {{ old('prefecture') == '北海道' ? 'selected' : '' }}>北海道</option>
                            <option value="青森県" {{ old('prefecture') == '青森県' ? 'selected' : '' }}>青森県</option>
                            <option value="岩手県" {{ old('prefecture') == '岩手県' ? 'selected' : '' }}>岩手県</option>
                            <option value="宮城県" {{ old('prefecture') == '宮城県' ? 'selected' : '' }}>宮城県</option>
                            <option value="秋田県" {{ old('prefecture') == '秋田県' ? 'selected' : '' }}>秋田県</option>
                            <option value="山形県" {{ old('prefecture') == '山形県' ? 'selected' : '' }}>山形県</option>
                            <option value="福島県" {{ old('prefecture') == '福島県' ? 'selected' : '' }}>福島県</option>
                            <option value="茨城県" {{ old('prefecture') == '茨城県' ? 'selected' : '' }}>茨城県</option>
                            <option value="栃木県" {{ old('prefecture') == '栃木県' ? 'selected' : '' }}>栃木県</option>
                            <option value="群馬県" {{ old('prefecture') == '群馬県' ? 'selected' : '' }}>群馬県</option>
                            <option value="埼玉県" {{ old('prefecture') == '埼玉県' ? 'selected' : '' }}>埼玉県</option>
                            <option value="千葉県" {{ old('prefecture') == '千葉県' ? 'selected' : '' }}>千葉県</option>
                            <option value="東京都" {{ old('prefecture') == '東京都' ? 'selected' : '' }}>東京都</option>
                            <option value="神奈川県" {{ old('prefecture') == '神奈川県' ? 'selected' : '' }}>神奈川県</option>
                            <option value="新潟県" {{ old('prefecture') == '新潟県' ? 'selected' : '' }}>新潟県</option>
                            <option value="富山県" {{ old('prefecture') == '富山県' ? 'selected' : '' }}>富山県</option>
                            <option value="石川県" {{ old('prefecture') == '石川県' ? 'selected' : '' }}>石川県</option>
                            <option value="福井県" {{ old('prefecture') == '福井県' ? 'selected' : '' }}>福井県</option>
                            <option value="山梨県" {{ old('prefecture') == '山梨県' ? 'selected' : '' }}>山梨県</option>
                            <option value="長野県" {{ old('prefecture') == '長野県' ? 'selected' : '' }}>長野県</option>
                            <option value="岐阜県" {{ old('prefecture') == '岐阜県' ? 'selected' : '' }}>岐阜県</option>
                            <option value="静岡県" {{ old('prefecture') == '静岡県' ? 'selected' : '' }}>静岡県</option>
                            <option value="愛知県" {{ old('prefecture') == '愛知県' ? 'selected' : '' }}>愛知県</option>
                            <option value="三重県" {{ old('prefecture') == '三重県' ? 'selected' : '' }}>三重県</option>
                            <option value="滋賀県" {{ old('prefecture') == '滋賀県' ? 'selected' : '' }}>滋賀県</option>
                            <option value="京都府" {{ old('prefecture') == '京都府' ? 'selected' : '' }}>京都府</option>
                            <option value="大阪府" {{ old('prefecture') == '大阪府' ? 'selected' : '' }}>大阪府</option>
                            <option value="兵庫県" {{ old('prefecture') == '兵庫県' ? 'selected' : '' }}>兵庫県</option>
                            <option value="奈良県" {{ old('prefecture') == '奈良県' ? 'selected' : '' }}>奈良県</option>
                            <option value="和歌山県" {{ old('prefecture') == '和歌山県' ? 'selected' : '' }}>和歌山県</option>
                            <option value="鳥取県" {{ old('prefecture') == '鳥取県' ? 'selected' : '' }}>鳥取県</option>
                            <option value="島根県" {{ old('prefecture') == '島根県' ? 'selected' : '' }}>島根県</option>
                            <option value="岡山県" {{ old('prefecture') == '岡山県' ? 'selected' : '' }}>岡山県</option>
                            <option value="広島県" {{ old('prefecture') == '広島県' ? 'selected' : '' }}>広島県</option>
                            <option value="山口県" {{ old('prefecture') == '山口県' ? 'selected' : '' }}>山口県</option>
                            <option value="徳島県" {{ old('prefecture') == '徳島県' ? 'selected' : '' }}>徳島県</option>
                            <option value="香川県" {{ old('prefecture') == '香川県' ? 'selected' : '' }}>香川県</option>
                            <option value="愛媛県" {{ old('prefecture') == '愛媛県' ? 'selected' : '' }}>愛媛県</option>
                            <option value="高知県" {{ old('prefecture') == '高知県' ? 'selected' : '' }}>高知県</option>
                            <option value="福岡県" {{ old('prefecture') == '福岡県' ? 'selected' : '' }}>福岡県</option>
                            <option value="佐賀県" {{ old('prefecture') == '佐賀県' ? 'selected' : '' }}>佐賀県</option>
                            <option value="長崎県" {{ old('prefecture') == '長崎県' ? 'selected' : '' }}>長崎県</option>
                            <option value="熊本県" {{ old('prefecture') == '熊本県' ? 'selected' : '' }}>熊本県</option>
                            <option value="大分県" {{ old('prefecture') == '大分県' ? 'selected' : '' }}>大分県</option>
                            <option value="宮崎県" {{ old('prefecture') == '宮崎県' ? 'selected' : '' }}>宮崎県</option>
                            <option value="鹿児島県" {{ old('prefecture') == '鹿児島県' ? 'selected' : '' }}>鹿児島県</option>
                            <option value="沖縄県" {{ old('prefecture') == '沖縄県' ? 'selected' : '' }}>沖縄県</option>
                        </select>
                        @error('prefecture')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 市区町村 -->
                    <div class="mb-4">
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            市区町村 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="city" 
                               name="city" 
                               class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                               placeholder="渋谷区"
                               value="{{ old('city') }}"
                               required>
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 番地 -->
                    <div class="mb-4">
                        <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-2">
                            番地 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="address_line1" 
                               name="address_line1" 
                               class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                               placeholder="1-2-3"
                               value="{{ old('address_line1') }}"
                               required>
                        @error('address_line1')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 建物名・部屋番号 -->
                    <div class="mb-4">
                        <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-2">
                            建物名・部屋番号
                        </label>
                        <input type="text" 
                               id="address_line2" 
                               name="address_line2" 
                               class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                               placeholder="○○マンション101号室"
                               value="{{ old('address_line2') }}">
                        @error('address_line2')
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

// 郵便番号の自動フォーマット
document.getElementById('postal_code').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9]/g, '');
    if (value.length >= 3) {
        value = value.slice(0, 3) + '-' + value.slice(3, 7);
    }
    e.target.value = value;
});
</script>
@endsection 