<?php

namespace Tests\Feature\Models;

use Illuminate\Http\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * User listing fields
     *
    */
    protected array $listFields = [
        'address',
        'avatar',
        'created_at',
        'email',
        'email_verified_at',
        'first_name',
        'is_marketing',
        'last_login_at',
        'last_name',
        'phone_number',
        'updated_at',
        'uuid'
    ];

    /**
     * User filters
     *
    */
    protected array $filters = [
        'first_name',
        'email',
        'phone',
        'address',
        'created_at',
        'marketing'
    ];

    /**
     * Set up user test
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->validInput = [
            'uuid' => fake()->uuid(),
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'is_admin' => 0,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'userpassword', // userpassword
            'password_confirmation' => 'userpassword', // userpassword
            // 'remember_token' => Str::random(10),
            'avatar' => fake()->uuid(),
            'address' => fake()->streetAddress,
            'phone_number' => fake()->phoneNumber(),
            'is_marketing' => fake()->randomElement([0, 1])
        ];
    }

    public function test_can_show(): void
    {
        $this->markTestSkipped('Test skipped from base test class');
    }

    public function test_can_see_all(): void
    {
        $this->markTestSkipped('Test skipped from base test class');
    }

    public function test_can_delete(): void
    {
        $this->markTestSkipped('Test skipped from base test class');
    }

    public function test_cannot_login_without_credentials(): void
    {
        $response = $this->postJson($this->getBaseUrl() . 'login', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_can_login_a_user_with_credentials(): void
    {
        $response = $this->postJson($this->getBaseUrl() . 'login', [
            'email' => $this->user->email,
            'password' => $this->validInput['password']
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token'
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_can_login_a_admin_with_credentials(): void
    {
        $response = $this->postJson($this->getBaseUrl('admin') . 'login', [
            'email' => config('app.admin_email'),
            'password' => config('app.admin_password')
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token'
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_cannot_login_a_user_with_credentials_to_admin_route(): void
    {
        $response = $this->postJson($this->getBaseUrl('admin') . 'login', [
            'email' => $this->user->email,
            'password' => $this->validInput['password']
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_login_a_admin_with_credentials_to_user_route(): void
    {
        $response = $this->postJson($this->getBaseUrl() . 'login', [
            'email' => config('app.admin_email'),
            'password' => config('app.admin_password')
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_logout(): void
    {
        auth()->login($this->user);
        $response = $this->getJson($this->getBaseUrl() . 'logout');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => [],
            'error',
            'errors',
            'extra'
        ]);
    }

    /**
     * Get base url
     *
     */
    protected function getBaseUrl($resource = null): string
    {
        return '/api/v1/' . ($resource ?? $this->resource) . '/';
    }

    /**
     * Get headers
     */
    protected function getHeaders()
    {
        return [];
    }
}
