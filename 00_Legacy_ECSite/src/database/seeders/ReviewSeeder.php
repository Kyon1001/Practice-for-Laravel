<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyers = User::where('type', 'buyer')->get();
        $products = Product::all();

        // 各商品にランダムなレビューを追加
        foreach ($products as $product) {
            // 商品ごとに1-5個のレビューを追加
            $reviewCount = rand(1, 5);
            $selectedBuyers = $buyers->random(min($reviewCount, $buyers->count()));

            foreach ($selectedBuyers as $buyer) {
                Review::create([
                    'product_id' => $product->id,
                    'buyer_id' => $buyer->id,
                    'rating' => rand(3, 5), // 3-5の評価
                    'comment' => $this->getRandomComment(),
                ]);
            }
        }
    }

    /**
     * ランダムなコメントを取得
     */
    private function getRandomComment()
    {
        $comments = [
            '思っていた通りの商品でした。満足しています。',
            '品質が良く、長く使えそうです。',
            '配送も早く、梱包も丁寧でした。',
            'コストパフォーマンスが良いです。',
            '期待以上の商品でした。また利用したいと思います。',
            '使いやすく、デザインも気に入っています。',
            '友人にもおすすめしたい商品です。',
            '価格の割に品質が良いと思います。',
            '丁寧な対応をしていただき、ありがとうございました。',
            '商品の説明通りで、満足です。',
            null, // コメントなしの場合もある
            null,
            null,
        ];

        return $comments[array_rand($comments)];
    }
}
