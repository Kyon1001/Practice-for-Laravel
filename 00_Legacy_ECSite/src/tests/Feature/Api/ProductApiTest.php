<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function can_get_products_list_via_api()
    {
        $products = Product::factory()->count(5)->create([
            'status' => 'active',
            'stock' => 10
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'description',
                             'price',
                             'stock',
                             'status',
                             'category',
                             'seller'
                         ]
                     ],
                     'pagination' => [
                         'current_page',
                         'per_page',
                         'total',
                         'last_page',
                         'has_more'
                     ]
                 ])
                 ->assertJson(['success' => true]);
    }

    /** @test */
    public function can_filter_products_by_category_via_api()
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

        $response = $this->getJson('/api/products?category=smartphones');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $products = $response->json('data');
        $this->assertTrue(collect($products)->contains('id', $product1->id));
        $this->assertFalse(collect($products)->contains('id', $product2->id));
    }

    /** @test */
    public function can_filter_products_by_price_range_via_api()
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

        $response = $this->getJson('/api/products?min_price=5000&max_price=20000');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $products = $response->json('data');
        $this->assertTrue(collect($products)->contains('id', $product1->id));
        $this->assertFalse(collect($products)->contains('id', $product2->id));
    }

    /** @test */
    public function can_search_products_via_api()
    {
        $product1 = Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Samsung Galaxy S24',
            'status' => 'active',
            'stock' => 3
        ]);

        $response = $this->getJson('/api/products?search=iPhone');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $products = $response->json('data');
        $this->assertTrue(collect($products)->contains('id', $product1->id));
        $this->assertFalse(collect($products)->contains('id', $product2->id));
    }

    /** @test */
    public function can_sort_products_via_api()
    {
        $product1 = Product::factory()->create([
            'name' => 'Product A',
            'price' => 50000,
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Product B',
            'price' => 10000,
            'status' => 'active',
            'stock' => 3
        ]);

        $response = $this->getJson('/api/products?sort=price_low');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $products = $response->json('data');
        $this->assertEquals($product2->id, $products[0]['id']); // 安い商品が最初
        $this->assertEquals($product1->id, $products[1]['id']); // 高い商品が次
    }

    /** @test */
    public function can_get_product_detail_via_api()
    {
        $product = Product::factory()->create([
            'status' => 'active',
            'stock' => 10
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'product' => [
                             'id',
                             'name',
                             'description',
                             'price',
                             'stock',
                             'status',
                             'category',
                             'seller'
                         ],
                         'related_products',
                         'average_rating',
                         'review_count'
                     ]
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'product' => [
                             'id' => $product->id,
                             'name' => $product->name
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function cannot_get_inactive_product_via_api()
    {
        $product = Product::factory()->create([
            'status' => 'inactive'
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => '商品が見つかりません。'
                 ]);
    }

    /** @test */
    public function can_get_categories_via_api()
    {
        $parentCategory = Category::factory()->create(['parent_id' => null]);
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->getJson('/api/products/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'slug',
                             'children'
                         ]
                     ]
                 ])
                 ->assertJson(['success' => true]);
    }

    /** @test */
    public function can_get_product_suggestions_via_api()
    {
        $product1 = Product::factory()->create([
            'name' => 'iPhone 15 Pro Max',
            'status' => 'active',
            'stock' => 5
        ]);

        $product2 = Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'status' => 'active',
            'stock' => 3
        ]);

        $product3 = Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'status' => 'active',
            'stock' => 2
        ]);

        $response = $this->getJson('/api/products/suggestions?q=iPhone');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name'
                         ]
                     ]
                 ])
                 ->assertJson(['success' => true]);

        $suggestions = $response->json('data');
        $suggestionIds = collect($suggestions)->pluck('id')->toArray();
        
        $this->assertContains($product1->id, $suggestionIds);
        $this->assertContains($product2->id, $suggestionIds);
        $this->assertNotContains($product3->id, $suggestionIds);
    }

    /** @test */
    public function suggestions_api_returns_empty_for_short_query()
    {
        $response = $this->getJson('/api/products/suggestions?q=i');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => []
                 ]);
    }

    /** @test */
    public function api_pagination_works_correctly()
    {
        Product::factory()->count(25)->create([
            'status' => 'active',
            'stock' => 10
        ]);

        $response = $this->getJson('/api/products?per_page=10');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $pagination = $response->json('pagination');
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(25, $pagination['total']);
        $this->assertEquals(3, $pagination['last_page']);
        $this->assertTrue($pagination['has_more']);
    }

    /** @test */
    public function api_respects_per_page_limit()
    {
        Product::factory()->count(100)->create([
            'status' => 'active',
            'stock' => 10
        ]);

        // 最大50件まで制限されているかテスト
        $response = $this->getJson('/api/products?per_page=100');

        $response->assertStatus(200);
        $pagination = $response->json('pagination');
        $this->assertEquals(50, $pagination['per_page']);
    }

    /** @test */
    public function only_active_products_with_stock_returned_via_api()
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

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $products = $response->json('data');
        $productIds = collect($products)->pluck('id')->toArray();

        $this->assertContains($activeProduct->id, $productIds);
        $this->assertNotContains($inactiveProduct->id, $productIds);
        $this->assertNotContains($outOfStockProduct->id, $productIds);
    }
}
