<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Create a new user instance.
     *
     * @return array
     */
    private function validCredentials()
    {
        $password   = $this->faker->password;
        $this->user = User::factory()->create(['password' => Hash::make($password)]);

        return [
            'email'    => $this->user->email,
            'password' => $password,
        ];
    }

    public function test_user_can_view_a_login_form()
    {
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function test_an_admin_can_login()
    {
        $credentials = $this->validCredentials();

        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/home');
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
    }

    public function test_a_user_can_not_login_without_credentials()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors([
                    'email'    => 'The email field is required.',
                    'password' => 'The password field is required.',
                ]);
    }

    public function test_a_user_can_not_login_without_email()
    {
        $credentials = $this->validCredentials();
        unset($credentials['email']);

        $response = $this->post('/login', $credentials);

        $response->assertSessionHasErrors([
                    'email' => 'The email field is required.',
                ]);
    }

    public function test_a_user_can_not_login_without_password()
    {
        $credentials = $this->validCredentials();
        unset($credentials['password']);

        $response = $this->post('/login', $credentials);

        $response->assertSessionHasErrors([
                    'password' => 'The password field is required.',
                ]);
    }
}
