<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    public function test_login_redirects_to_products(): void {
        // create a user in the db so we can try to log in as that user
        $user = User::create(
            [
                'name' => 'Danny',
                'email' => 'danny@gmail.com',
                'password' => bcrypt('password123')
            ]
        );

        // login as the new user (Laravel Breeze automatically created this login route)
        $response = $this->post('/login', [
            'email' => 'danny@gmail.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('products');
    }


    public function test_unauthenticated_user_cannot_access_product(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
}
