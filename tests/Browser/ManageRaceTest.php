<?php

namespace Tests\Browser;

use App\User;
use Tests\Helpers\HandlesRaces;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageRaceTest extends DuskTestCase
{
    use DatabaseMigrations;
    use HandlesRaces;

    /**
    * @test
    */
    public function a_user_can_create_a_race()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(create(User::class))
                    ->visit('/lobby')
                    ->click('@create-game-button')
                    ->waitForLocation('/race/create')
                    ->click('@create-game-button')
                    ->waitForLocation('/race/1')
                    ->assertPathIs('/race/1')
                    ->waitForText("Leave game")
                    ->assertSee("Leave game");
        });
    }

    /**
    * @test
    */
    public function a_user_can_leave_a_race()
    {
        $this->createRaceAndAttachUsers(2);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->users[1])
                    ->visit('/race/1')
                    ->waitForText('Leave game') 
                    ->click('@leave-game-button')
                    ->waitForText('Confirm')
                    ->click('.button-0')
                    ->waitForLocation('/lobby')
                    ->assertPathIs('/lobby')
                    ->assertSee("Lobby");
        });

        $this->assertCount(1, $this->race->fresh()->participants);
        $this->assertFalse($this->race->fresh()->isInRace($this->users[1]));
    }
}
