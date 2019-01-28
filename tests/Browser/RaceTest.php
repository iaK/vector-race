<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\Helpers\HandlesRaces;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RaceTest extends DuskTestCase
{
    use HandlesRaces;
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function it_can_complete_a_race()
    {
        $this->createRaceAndAttachUsers();
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->users[0])
                    ->visit("/race/1")
                    ->waitForText("Start")
                    ->click("@start-game");
        });
    }
}
