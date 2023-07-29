<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    Use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // create test users
        $this->user = $this->createUser();
        $this->admin = $this->createUser(isAdmin: true);
    }

    /**
     * A basic feature test example.
     */
    public function test_homepage_contains_empty_table(): void
    {
        // get this page acting as the fake logged in user
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertSee(__('No products found'));
    }

    public function test_homepage_contains_non_empty_table(): void
    {
        // Arrange: (often multiple things to arrange) add any data, configure any variables. Prepare the scenario.
        $product = Product::create([
            'title' => 'Golf Club',
            'price' => 109.99,
        ]);
        // $user = $this->createUser();

        // Act: do some action (usually just 1). Call some api, url, function. Simulate the action of the user.
        $response = $this->actingAs($this->user)->get('/products');

        // Assert (often multiple assertions): check everything works as expected.
        $response->assertStatus(200);
        $response->assertDontSee(__('No products found'));
        $response->assertSee('Golf Club');
        $response->assertViewHas('products', function($collection) use($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record() {
        // create 11 records
        $products = Product::factory(11)->create(
            [
                'price' => 999
            ]
        );
        $lastProduct = $products->last();
        // $user = $this->createUser();

        // get the page
        $response = $this->actingAs($this->user)->get('/products');

        // ensure view doesn't contain the 11th product
        $response->assertViewHas('products', function($collection) use($lastProduct) {
            return !$collection->contains($lastProduct);
        });
    }

    public function test_admin_can_see_product_create_button() {
        $response = $this->actingAs($this->admin)->get('/products');

        $response->assertStatus(200);
        // check the products create link shows for admin user
        $response->assertSee('Add new product');
    }

    public function test_non_admin_cannot_see_product_create_button() {
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee('Add new product');
    }

    public function test_admin_can_access_product_create_page() {
        $response = $this->actingAs($this->admin)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_product_create_page() {
        $response = $this->actingAs($this->user)->get('/products/create');
        $response->assertStatus(403);
    }

    public function test_create_product_successful() {
        $product = [
            'title' => 'Golf Club',
            'price' => 100
        ];
        // create a product
        $response = $this->actingAs($this->admin)->post('/products', $product);

        // check the stuff that happens in the browser is all good
        $response->assertStatus(302);
        $response->assertRedirect('/products');

        // check the db contains the new product
        $this->assertDatabaseHas(table: 'products', data: $product);
        // check the latest product inserted into db has correct attributes
        $latestProduct = Product::latest()->first();
        $this->assertEquals($latestProduct->title, $product['title']);
        $this->assertEquals($latestProduct->price, $product['price']);
    }

    public function test_product_edit_contains_correct_values(): void {
        // create product
        $product = Product::factory()->create();

        // request this product's edit page
        $request = $this->actingAs($this->admin)->get('products/' . $product->id . '/edit');

        // assert that the page contains the correct data
        $request->assertStatus(200);
        $request->assertSee('value="' . $product->title . '"', false);
        $request->assertSee('value="' . $product->price . '"', false);
        $request->assertViewHas('product', $product);
    }

    public function test_product_update_validation_error_redirects_back_to_form(): void {
        $product = Product::factory()->create();

        $request = $this->actingAs($this->admin)->put('/products/' . $product->id, [
            'title' => '', // invalid
            'price' => '' // invalid
        ]);

        $request->assertStatus(302);
        $request->assertInvalid(['title', 'price']); // validation errors are stored in the session. There is also an assertSessionHas() method for checking any session values 
    }

    public function test_product_delete_successful() {
        // make a product
        $product = Product::factory()->create();
        
        // delete the product
        $request = $this->actingAs($this->admin)->delete('products/' . $product->id);

        // assert we're redirected correctly
        $request->assertStatus(302);
        $request->assertRedirect('products');

        // assert db doesn't contain this product
        $this->assertDatabaseMissing('products', $product->toArray());
        $this->assertDatabaseEmpty('products'); // products table should be empty
    }

    public function test_api_returns_products_list(): void {
        $product = Product::factory()->create();

        $response = $this->getJson('api/products');

        $response->assertJson([$product->toArray()]);
    }

    public function test_api_products_store_successful(): void {
        $product = [
            'title' => 'Golf Club',
            'price' => 100
        ];

        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(201);
        $response->assertJson($product);
    }

    public function test_api_product_invalid_store_returns_error(): void {
        $product = [
            'title' => '',
            'price' => 100
        ];

        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(422);
    }

    private function createUser(bool $isAdmin = false): User {
        return User::factory()->create([
            'is_admin' => $isAdmin
        ]);
    }

}
