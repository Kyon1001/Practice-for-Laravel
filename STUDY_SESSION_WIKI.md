# 🛒 Laravel ECサイト開発 勉強会Wiki

## 📋 プロジェクト概要
**マルチベンダー型ECプラットフォーム**を Laravel で構築
- 管理者・出品者・購入者の3つの役割
- 商品管理、カート機能、注文処理を実装
- 無料デプロイまで完了

---

## 🎯 開発ステップと重要ポイント

### 1. 要件定義・ER設計 ✅**完了**

#### 🔑 **重要ポイント**
- **ユーザー種別を明確に定義**（管理者/出品者/購入者）
- **データベース設計でリレーションを正確に**
  - users ↔ products（1対多）
  - products ↔ categories（多対1）
  - orders ↔ order_items（1対多）

#### ⚠️ **注意点**
```sql
-- 例：正規化を意識したテーブル設計
products: id, seller_id, category_id, name, price, stock
order_items: id, order_id, product_id, quantity, price
```

---

### 2. 環境設定 ✅**完了**

#### 🔑 **重要ポイント**
- **Docker + Laravel + MySQL** の組み合わせ
- **TailwindCSS** でモダンなUI
- **開発環境の統一化**

#### ⚠️ **注意点**
```yaml
# docker-compose.yml で環境変数管理
environment:
  - DB_HOST=mysql
  - DB_DATABASE=laravel_ec
  - APP_ENV=local
```

---

### 3. フロントページの作成 ✅**完了**

#### 🔑 **重要ポイント**
- **レスポンシブデザイン**（スマホ対応必須）
- **商品検索・カテゴリー絞り込み**機能
- **直感的なナビゲーション**

#### ⚠️ **注意点**
```php
// Blade テンプレートでの条件分岐
@auth
    <a href="{{ route('cart.index') }}">カート</a>
@else
    <a href="{{ route('login') }}">ログイン</a>
@endauth
```

---

### 4. 商品ページの作成 ✅**完了**

#### 🔑 **重要ポイント**
- **商品詳細の充実**（画像・説明・価格・在庫）
- **カートに追加**機能
- **レビュー表示**

#### ⚠️ **注意点**
```php
// 在庫チェックの実装
public function addToCart(Request $request, Product $product)
{
    if ($product->stock < $request->quantity) {
        return back()->with('error', '在庫が不足しています');
    }
    // カート追加処理
}
```

---

### 5. カート画面の作成 ✅**完了**

#### 🔑 **重要ポイント**
- **数量変更・削除**機能
- **合計金額の自動計算**
- **購入画面への導線**

#### ⚠️ **注意点**
```javascript
// フロントエンドでのリアルタイム計算
function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item-price').forEach(item => {
        total += parseFloat(item.dataset.price) * parseInt(item.dataset.quantity);
    });
    document.getElementById('total').textContent = total.toLocaleString();
}
```

---

### 6. 購入画面の作成 🔄**進行中**

#### 🔑 **重要ポイント**
- **配送先情報の入力**
- **支払い方法の選択**
- **注文確認画面**

#### ⚠️ **注意点**
```php
// トランザクション処理で整合性確保
DB::transaction(function () use ($orderData) {
    $order = Order::create($orderData);
    foreach ($cartItems as $item) {
        OrderItem::create([...]);
        Product::find($item->product_id)->decrement('stock', $item->quantity);
    }
    Cart::where('user_id', auth()->id())->delete();
});
```

---

### 7. API・データベース連携 🔄**進行中**

#### 🔑 **重要ポイント**
- **RESTful API設計**
- **認証・認可の実装**
- **エラーハンドリング**

#### ⚠️ **注意点**
```php
// API レスポンスの統一
return response()->json([
    'success' => true,
    'data' => $products,
    'message' => '商品一覧を取得しました'
], 200);
```

---

### 8. CI/CD実装 ✅**完了**

#### 🔑 **重要ポイント**
- **GitHub Actions** で自動テスト
- **コード品質チェック**
- **自動デプロイ**

#### ⚠️ **注意点**
```yaml
# .github/workflows/ci.yml
- name: Run Tests
  run: |
    cd src
    php artisan test
    php artisan migrate:fresh --seed --env=testing
```

---

### 9. 無料デプロイ ✅**完了**

#### 🔑 **重要ポイント**
- **Render.com推奨**（512MB RAM、PostgreSQL付き）
- **環境変数の設定**
- **本番用設定の調整**

#### ⚠️ **注意点**
```bash
# 本番環境での設定
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql  # PostgreSQL使用
```

---

## 🎯 **最重要な学び**

### 1. **設計の重要性**
- 最初のER図とAPI設計が全体の品質を決める
- 後から変更するコストは10倍以上

### 2. **セキュリティ対策**
```php
// 必須のセキュリティ対策
- CSRF保護: @csrf
- SQLインジェクション対策: Eloquent ORM使用
- XSS対策: {{ $variable }} でエスケープ
```

### 3. **ユーザビリティ**
- **エラーメッセージは分かりやすく**
- **ローディング状態を表示**
- **スマホファースト設計**

### 4. **パフォーマンス**
```php
// N+1問題の回避
$products = Product::with(['category', 'images'])->get();
```

---

## 🚀 **次のステップ**

1. **決済システム統合**（Stripe等）
2. **メール通知機能**
3. **管理画面の充実**
4. **パフォーマンス最適化**
5. **セキュリティ監査**

---

## 📚 **参考資料**
- [Laravel公式ドキュメント](https://laravel.com/docs)
- [TailwindCSS](https://tailwindcss.com/)
- [Render.com デプロイガイド](https://render.com/docs)

---

**�� 質問・相談はいつでもどうぞ！** 