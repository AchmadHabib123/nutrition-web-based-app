<?php
namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    public function test_admin_redirects_to_admin_dashboard()
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_redirects_to_user_dashboard()
    {
        $user = User::factory()->make(['role' => 'user']);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('user.dashboard'));
    }
}
