<?php
namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_redirect_to_admin_dashboard()
    {
        $admin = User::factory()->create([
            'email' => 'ahli-gizi@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ahli-gizi'
        ]);

        $response = $this->post(route('login'), [
            'email' => 'ahli-gizi@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('ahli-gizi.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_user_can_login_and_redirect_to_user_dashboard()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'tenaga-gizi'
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('tenaga-gizi.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_fails()
    {
        $response = $this->post(route('login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
