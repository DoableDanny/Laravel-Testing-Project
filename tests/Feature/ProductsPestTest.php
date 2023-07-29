<?php

use App\Models\Product;

beforeEach(function() {
    $this->user = createUser();
    $this->admin = createUser(isAdmin: true);
});

test('homepage contains empty table', function () {
   $this->actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertSee(__('No products found'));
});

test('homepage contains non empty table', function() {
     $product = Product::create([
        'title' => 'Golf Club',
        'price' => 109.99,
    ]);

    $this->actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertDontSee(__('No products found'))
        ->assertSee('Golf Club')
        ->assertViewHas('products', function($collection) use($product) {
        return $collection->contains($product);
    });
});

test('create product successful', function () {
    $product = [
        'title' => 'Golf Club',
        'price' => 100.00
    ];

    $this->actingAs($this->admin)
            ->post('/products', $product)
            ->assertStatus(302)
            ->assertRedirect('/products');

    $this->assertDatabaseHas(table: 'products', data: $product);
    // check the latest product inserted into db has correct attributes
    $latestProduct = Product::latest()->first();
    expect($latestProduct->title)->toBe($product['title']);
    expect($latestProduct->price)->toBe($product['price']);
});