<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;

class CartTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'status' => 'active',
            'stock' => 10,
            'price' => 1000
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_cart()
    {
        $response = $this->actingAs($this->user)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
        $response->assertViewHas('cartItems');
        $response->assertViewHas('totalAmount');
    }

    /** @test */
    public function guest_cannot_view_cart()
    {
        $response = $this->get(route('cart.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->user)
            ->post(route('cart.add', $this->product), [
                'quantity' => 2
            ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // データベースを確認
        $this->assertDatabaseHas('carts', [
            'buyer_id' => $this->user->id
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    /** @test */
    public function user_cannot_add_out_of_stock_product_to_cart()
    {
        $this->product->update(['stock' => 0]);

        $response = $this->actingAs($this->user)
            ->post(route('cart.add', $this->product), [
                'quantity' => 1
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // カートに追加されていないことを確認
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $this->product->id
        ]);
    }

    /** @test */
    public function user_cannot_add_more_than_available_stock()
    {
        $this->product->update(['stock' => 5]);

        $response = $this->actingAs($this->user)
            ->post(route('cart.add', $this->product), [
                'quantity' => 10
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('quantity');
    }

    /** @test */
    public function adding_same_product_increases_quantity()
    {
        // 最初に商品を追加
        $this->actingAs($this->user)
            ->post(route('cart.add', $this->product), [
                'quantity' => 2
            ]);

        // 同じ商品を再度追加
        $response = $this->actingAs($this->user)
            ->post(route('cart.add', $this->product), [
                'quantity' => 3
            ]);

        $response->assertRedirect(route('cart.index'));

        // 数量が合計されていることを確認
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
    }

    /** @test */
    public function user_can_update_cart_item_quantity()
    {
        // カートアイテムを作成
        $cart = Cart::create(['buyer_id' => $this->user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('cart.update', $cartItem), [
                'quantity' => 5
            ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // 数量が更新されていることを確認
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_cart_item()
    {
        $otherUser = User::factory()->create();
        $otherCart = Cart::create(['buyer_id' => $otherUser->id]);
        $otherCartItem = CartItem::create([
            'cart_id' => $otherCart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('cart.update', $otherCartItem), [
                'quantity' => 5
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_remove_item_from_cart()
    {
        // カートアイテムを作成
        $cart = Cart::create(['buyer_id' => $this->user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('cart.remove', $cartItem));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // カートアイテムが削除されていることを確認
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }

    /** @test */
    public function user_can_clear_entire_cart()
    {
        // 複数のカートアイテムを作成
        $cart = Cart::create(['buyer_id' => $this->user->id]);
        $product2 = Product::factory()->create(['status' => 'active', 'stock' => 5]);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('cart.clear'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        // すべてのカートアイテムが削除されていることを確認
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id
        ]);
    }

    /** @test */
    public function cart_count_api_returns_correct_count()
    {
        // カートアイテムを作成
        $cart = Cart::create(['buyer_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 3
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('cart.count'));

        $response->assertStatus(200);
        $response->assertJson(['count' => 3]);
    }

    /** @test */
    public function cart_count_returns_zero_for_guest()
    {
        $response = $this->get(route('cart.count'));

        $response->assertStatus(200);
        $response->assertJson(['count' => 0]);
    }

    /** @test */
    public function cart_total_is_calculated_correctly()
    {
        $product2 = Product::factory()->create([
            'status' => 'active',
            'stock' => 5,
            'price' => 2000
        ]);

        $cart = Cart::create(['buyer_id' => $this->user->id]);
        
        // 商品1: 1000円 × 2個 = 2000円
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        // 商品2: 2000円 × 1個 = 2000円
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($this->user)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalAmount', 4000);
    }
}
