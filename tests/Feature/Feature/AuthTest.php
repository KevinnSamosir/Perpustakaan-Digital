<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration with valid data
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user',
                    'token',
                ])->assertJson([
                    'message' => 'User registered successfully',
                    'user' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'role' => 'member',
                    ],
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test registration fails with existing email
     */
    public function test_registration_fails_with_existing_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test registration fails with short password
     */
    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => 'member',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test registration fails with mismatched password confirmation
     */
    public function test_registration_fails_with_mismatched_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
            'role' => 'member',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'user',
                    'token',
                ])->assertJson([
                    'message' => 'Login successful',
                    'user' => [
                        'email' => 'john@example.com',
                    ],
                ]);
    }

    /**
     * Test login fails with invalid email
     */
    public function test_login_fails_with_invalid_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid email or password',
                ]);
    }

    /**
     * Test login fails with wrong password
     */
    public function test_login_fails_with_wrong_password(): void
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid email or password',
                ]);
    }

    /**
     * Test get authenticated user
     */
    public function test_get_authenticated_user(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                        ->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure(['message', 'user']);
    }

    /**
     * Test logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                        ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Logout successful',
                ]);
    }

    /**
     * Test accessing protected endpoint without token
     */
    public function test_accessing_protected_endpoint_without_token(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }
}
