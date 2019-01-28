<?php

namespace Tests\Feature;

use App\Race;
use App\User;
use Tests\TestCase;
use App\Events\PlayerWon;
use App\Events\RaceCreated;
use App\Events\RaceStarted;
use App\Events\TurnChanged;
use App\Events\PlayerFailed;
use Tests\Helpers\HandlesUsers;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use HandlesUsers;

    /**
    * @test
    */
    public function it_redirects_to_login_if_not_signed_in()
    {
        $this->get("/lobby")
            ->assertRedirect("/login");
    }

    /**
    * @test
    */
    public function signed_in_users_gets_redirected_on_home()
    {
        $this->createAndLogin();

        $this->get("/")
            ->assertRedirect("/lobby");
    }

    /**
    * @test
    */
    public function it_can_log_out()
    {
        $user = $this->createAndLogin();
        $this->assertAuthenticatedAs($user);

        $this->post("/logout");

        $this->assertGuest();
    }

    /**
    * @test
    */
    public function it_can_signup()
    {
        $this->post("/register", [
            "email" => "test@test.com",
            "username" => "tester",
            "password" => "abc123",
            "password_confirmation" => "abc123",
        ])->assertStatus(302);

        $this->assertDatabaseHas("users", [
            "email" => "test@test.com",
        ]);
    }
}
