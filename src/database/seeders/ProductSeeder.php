<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = User::where('type', 'seller')->get();
        $categories = Category::all();

        // スマートフォンカテゴリーの商品
        $smartphoneCategory = Category::where('slug', 'smartphones')->first();
        if ($smartphoneCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $smartphoneCategory->id,
                'name' => 'iPhone 15 Pro',
                'description' => '最新のiPhone 15 Pro。高性能カメラと長時間バッテリーが特徴。',
                'price' => 149800,
                'stock' => 10,
                'status' => 'active',
            ]);

            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $smartphoneCategory->id,
                'name' => 'Samsung Galaxy S24',
                'description' => 'Android最新フラッグシップモデル。AIカメラ機能搭載。',
                'price' => 119800,
                'stock' => 15,
                'status' => 'active',
            ]);
        }

        // パソコンカテゴリーの商品
        $computerCategory = Category::where('slug', 'computers')->first();
        if ($computerCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $computerCategory->id,
                'name' => 'MacBook Pro 14インチ',
                'description' => 'M3チップ搭載のMacBook Pro。プロフェッショナル向け。',
                'price' => 248000,
                'stock' => 5,
                'status' => 'active',
            ]);

            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $computerCategory->id,
                'name' => 'Dell XPS 13',
                'description' => '軽量で高性能なWindows ノートパソコン。',
                'price' => 159800,
                'stock' => 8,
                'status' => 'active',
            ]);
        }

        // ファッション商品
        $mensCategory = Category::where('slug', 'mens-fashion')->first();
        if ($mensCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $mensCategory->id,
                'name' => 'カジュアルTシャツ',
                'description' => '綿100%の快適なTシャツ。様々なカラーバリエーション。',
                'price' => 2980,
                'stock' => 50,
                'status' => 'active',
            ]);
        }

        $womensCategory = Category::where('slug', 'womens-fashion')->first();
        if ($womensCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $womensCategory->id,
                'name' => 'エレガントワンピース',
                'description' => '上品なデザインのワンピース。オフィスにも普段使いにも。',
                'price' => 12800,
                'stock' => 20,
                'status' => 'active',
            ]);
        }

        // 本・雑誌
        $booksCategory = Category::where('slug', 'books')->first();
        if ($booksCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $booksCategory->id,
                'name' => 'プログラミング入門書',
                'description' => '初心者向けのプログラミング学習書。図解付きでわかりやすい。',
                'price' => 3200,
                'stock' => 30,
                'status' => 'active',
            ]);
        }

        // キッチン用品
        $kitchenCategory = Category::where('slug', 'kitchen')->first();
        if ($kitchenCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $kitchenCategory->id,
                'name' => 'ステンレス包丁セット',
                'description' => 'プロ仕様のステンレス包丁3本セット。切れ味抜群。',
                'price' => 15800,
                'stock' => 12,
                'status' => 'active',
            ]);
        }

        // スポーツ・アウトドア
        $sportsCategory = Category::where('slug', 'sports-outdoors')->first();
        if ($sportsCategory) {
            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $sportsCategory->id,
                'name' => 'ランニングシューズ',
                'description' => '軽量で快適なランニングシューズ。クッション性に優れている。',
                'price' => 8900,
                'stock' => 25,
                'status' => 'active',
            ]);
        }
    }
}
