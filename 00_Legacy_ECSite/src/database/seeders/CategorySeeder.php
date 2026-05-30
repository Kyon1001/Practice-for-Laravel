<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 家電・電子機器
        $electronics = Category::create([
            'name' => '家電・電子機器',
            'slug' => 'electronics',
        ]);

        Category::create([
            'name' => 'スマートフォン',
            'slug' => 'smartphones',
            'parent_id' => $electronics->id,
        ]);

        Category::create([
            'name' => 'パソコン',
            'slug' => 'computers',
            'parent_id' => $electronics->id,
        ]);

        // ファッション
        $fashion = Category::create([
            'name' => 'ファッション',
            'slug' => 'fashion',
        ]);

        Category::create([
            'name' => 'メンズ',
            'slug' => 'mens-fashion',
            'parent_id' => $fashion->id,
        ]);

        Category::create([
            'name' => 'レディース',
            'slug' => 'womens-fashion',
            'parent_id' => $fashion->id,
        ]);

        // 本・雑誌
        Category::create([
            'name' => '本・雑誌',
            'slug' => 'books',
        ]);

        // ホーム・キッチン
        $home = Category::create([
            'name' => 'ホーム・キッチン',
            'slug' => 'home-kitchen',
        ]);

        Category::create([
            'name' => 'キッチン用品',
            'slug' => 'kitchen',
            'parent_id' => $home->id,
        ]);

        // スポーツ・アウトドア
        Category::create([
            'name' => 'スポーツ・アウトドア',
            'slug' => 'sports-outdoors',
        ]);
    }
}
