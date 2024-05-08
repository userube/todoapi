<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
  use DatabaseTransactions;
  use WithFaker;

  /**
   * Test user registration with valid data.
   *
   * @return void
   */
  public function test_user_registration_with_valid_data()
  {
    $userData = [
      'name' => $this->faker->name,
      'email' => $this->faker->unique()->safeEmail,
      'password' => 'password',
      'password_confirmation' => 'password',
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(201)
      ->assertJson([
        'status' => true,
        'message' => 'User registered successfully',
      ]);

    $this->assertDatabaseHas('users', [
      'name' => $userData['name'],
      'email' => $userData['email'],
    ]);
  }

  /**
   * Test user login with valid credentials.
   *
   * @return void
   */
  public function test_user_login_with_valid_credentials()
  {
    $password = 'password';
    $user = User::factory()->create([
      'password' => Hash::make($password),
    ]);

    $credentials = [
      'email' => $user->email,
      'password' => $password,
    ];

    $response = $this->postJson('/api/login', $credentials);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'access_token',
        'token_type',
        'expires_in',
      ]);
  }

  /**
   * Test user logout.
   *
   * @return void
   */
  public function test_user_logout()
  {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/logout');

    $response->assertStatus(200)
      ->assertJson([
        'message' => 'Successfully logged out',
      ]);
  }
}
