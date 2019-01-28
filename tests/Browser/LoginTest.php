<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
    * @test
    */
    public function a_user_can_login()
    {
        $user = create(User::class, [
            "email" => "tester@test.test",
            "password" => bcrypt("secret"),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type("@email", "tester@test.test")
                    ->type("@password", "secret")
                    ->click('@login-button')
                    ->waitForReload()
                    ->assertPathIs("/lobby");
        });
    }

    /**
    * @test
    */
    public function a_user_can_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/signup')
                    ->type("@email", "test@test.test")
                    ->type("@username", "tester")
                    ->type("@password", "secret")
                    ->type("@password-confirmation", "secret")
                    ->click('@signup-button')
                    ->waitForReload()
                    ->assertPathIs("/lobby");
        
            $this->assertDatabaseHas("users", [
                "email" => "test@test.test",
                "username" => "tester",
            ]);        
        });
    }
}
