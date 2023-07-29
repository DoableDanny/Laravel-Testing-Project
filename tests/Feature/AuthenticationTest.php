<?php

// Using PEST

test('unauthenticated user cannot access products')
    ->get('/products')
    ->assertStatus(302)
    ->assertRedirect('login');