<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public const email = 'seanphilipcruz@gmail.com';
    public const password = 'asdqwe123';
    public const wrong_password = 'zxcasdqwe123';

    /**
     * test registration if api can register
     *
     * @test
     */
    public function register_test()
    {
        $response = $this->post(route('register'), [
            'email' => self::email,
            'password' => self::password,
        ]);

        $response->assertStatus(201);
    }

    /**
     * test registration with the same email
     *
     * @test
     */
    public function register_with_same_email_test() {
        $response = $this->post(route('register'), [
            'email' => self::email,
            'password' => self::password,
        ]);

        $response->assertStatus(400);
    }

    /**
     * test if registered email and password exists in the database and the order tests.
     *
     * @test
     */
    public function login_test() {
        $response = $this->post(route('login'), [
            'email' => self::email,
            'password' => self::password,
        ]);

        $response->assertStatus(201);

        self::order_test($response->assertSee('access_token'));

        self::ordering_beyond_product_stocks_test($response->assertSee('access_token'));
    }

    /**
     * test logging in with wrong password for 6 times
     *
     * @test
     */
    public function login_with_wrong_password_test() {
        $response = $this->post(route('login'), [
           'email' => self::email,
           'password' => self::wrong_password
        ]);

        $response->assertStatus(401);
    }

    public function order_test($token) {
        $response = $this->post(route('place.order'), [
            'product_id' => 1,
            'quantity' => 5
        ], [
            'Authorization: Bearer '. $token
        ]);

        $response->assertStatus(201);
    }

    public function ordering_beyond_product_stocks_test($token) {
        $response = $this->post(route('place.order'), [
            'product_id' => 2,
            'quantity' => 9999
        ], [
            'Authorization: Bearer '. $token
        ]);

        $response->assertStatus(400);
    }
}
