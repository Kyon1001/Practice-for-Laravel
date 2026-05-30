<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function user_can_view_product_index_page()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function user_can_view_product_detail_page()
    {
        $product = Product::factory()->create([
            'status' => 'active',
            'stock' => 10
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product');
        $response->assertSee($product->name);
        $response->assertSee($product->description);
    }

    /** @test */
    public function user_cannot_view_inactive_product()
    {
        $product = Product::factory()->create([
            'status' => 'inactive'
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_search_products()
    {
        $product1 = Product::factory()->create([
            'name' => 'iPhone 15',
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'status' => 'active',
            'stock' => 3
        ]);

        $response = $this->get(route('products.search', ['q' => 'iPhone']));

        $response->assertStatus(200);
        $response->assertViewIs('products.search');
        $response->assertSee($product1->name);
        $response->assertDontSee($product2->name);
    }

    /** @test */
    public function user_can_filter_products_by_category()
    {
        $category1 = Category::factory()->create(['slug' => 'smartphones']);
        $category2 = Category::factory()->create(['slug' => 'computers']);

        $product1 = Product::factory()->create([
            'category_id' => $category1->id,
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'category_id' => $category2->id,
            'status' => 'active',
            'stock' => 3
        ]);

        $response = $this->get(route('products.index', ['category' => 'smartphones']));

        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertDontSee($product2->name);
    }

    /** @test */
    public function user_can_filter_products_by_price_range()
    {
        $product1 = Product::factory()->create([
            'price' => 10000,
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'price' => 50000,
            'status' => 'active',
            'stock' => 3
        ]);

        $response = $this->get(route('products.index', [
            'min_price' => 5000,
            'max_price' => 20000
        ]));

        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertDontSee($product2->name);
    }

    /** @test */
    public function products_can_be_sorted_by_price()
    {
        $product1 = Product::factory()->create([
            'name' => 'Expensive Product',
            'price' => 50000,
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Cheap Product',
            'price' => 10000,
            'status' => 'active',
            'stock' => 3
        ]);

        // 価格の安い順でソート
        $response = $this->get(route('products.index', ['sort' => 'price_low']));

        $response->assertStatus(200);
        
        // レスポンス内容を取得して順序を確認
        $content = $response->getContent();
        $pos1 = strpos($content, $product2->name); // 安い商品
        $pos2 = strpos($content, $product1->name); // 高い商品
        
        $this->assertTrue($pos1 < $pos2, 'Products should be sorted by price (low to high)');
    }

    /** @test */
    public function only_active_products_with_stock_are_displayed()
    {
        $activeProduct = Product::factory()->create([
            'status' => 'active',
            'stock' => 5
        ]);

        $inactiveProduct = Product::factory()->create([
            'status' => 'inactive',
            'stock' => 5
        ]);

        $outOfStockProduct = Product::factory()->create([
            'status' => 'active',
            'stock' => 0
        ]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee($activeProduct->name);
        $response->assertDontSee($inactiveProduct->name);
        $response->assertDontSee($outOfStockProduct->name);
    }
}
