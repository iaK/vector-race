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
    * @test
    */
    public function you_loose_if_you_go_off_course()
    {
        $this->createRaceAndAttachUsers(1);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->users[0])
                ->visit("/race/1")
                ->waitForText("Start")
                ->click("@start-race-button")
                ->pause(1000)
                ->waitForText("Your turn")
                ->keys(".chat-input", ["2"])
                ->waitForText("Your turn")
                ->pause(1000)
                ->keys(".chat-input", ["2"])
                ->waitForText("Fail!")
                ->assertSee("Fail!");
        });
    }

    /**
    * @test
    */
    public function you_loose_if_you_finish_from_the_wrong_way()
    {
        $this->createRaceAndAttachUsers(1);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->users[0])
                ->visit("/race/1")
                ->waitForText("Start")
                ->click("@start-race-button")
                ->pause(1000)
                ->waitForText("Your turn")
                ->keys(".chat-input", ["5"])
                ->pause(1000)
                ->waitForText("Your turn")
                ->keys(".chat-input", ["6"])
                ->pause(1000)
                ->waitForText("Your turn")
                ->keys(".chat-input", ["6"])
                ->pause(1000)
                ->waitForText("Your turn")
                ->keys(".chat-input", ["6"])
                ->waitForText("Fail!")
                ->assertSee("Fail!");
        });
    }

    /**
    * @test
    */
    public function it_can_complete_a_race()
    {
        $this->createRaceAndAttachUsers(1);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->users[0])
                    ->visit("/race/1")
                    ->waitForText("Start")
                    ->click("@start-race-button")
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->keys(".chat-input", ["5"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("1")
                    ->keys(".chat-input", ["8"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("2")
                    ->keys(".chat-input", ["6"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("3")
                    ->keys(".chat-input", ["6"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("4")
                    ->keys(".chat-input", ["2"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("5")
                    ->keys(".chat-input", ["2"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("6")
                    ->keys(".chat-input", ["4"])
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("7")
                    ->keys(".chat-input", ["4"])
                    ->pause(50)
                    ->keys(".chat-input", ["4"])
                    ->assertSee('Win!');
        });
    }

    /**
     * @test
     */
     public function two_players_can_complete_a_race()
     {
         $this->createRaceAndAttachUsers(2);

         $this->browse(function (Browser $first, Browser $second, Browser $third) {
            $first->loginAs($this->users[0])
                    ->visit("/race/1");

            $second->loginAs($this->users[1])
                    ->visit("/race/1")
                    ->waitForText("Leave");

            $first->waitForText("Start")
                    ->click("@start-race-button")
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->keys(".chat-input", ["5"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("test2")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("1")
                    ->keys(".chat-input", ["8"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p1")
                    ->keys(".chat-input", ["6"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("2")
                    ->keys(".chat-input", ["6"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p2")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("3")
                    ->keys(".chat-input", ["6"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p3")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("4")
                    ->keys(".chat-input", ["2"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p4")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("5")
                    ->keys(".chat-input", ["2"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p5")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("6")
                    ->keys(".chat-input", ["4"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p6")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("7")
                    ->keys(".chat-input", ["4"])
                    ->assertSee('Win!');

            $second->pause(50)
                    ->waitForText("Fail!")
                    ->assertSee('Fail!');
         });
     }

     /**
     * @test
     */
     public function three_players_can_complete_a_race()
     {
         $this->createRaceAndAttachUsers(3);

         $this->browse(function (Browser $first, Browser $second, Browser $third) {
            $first->loginAs($this->users[0])
                    ->visit("/race/1");

            $second->loginAs($this->users[1])
                    ->visit("/race/1")
                    ->waitForText("Leave");

            $third->loginAs($this->users[2])
                    ->visit("/race/1")
                    ->waitForText("Leave");

            $first->waitForText("Start")
                    ->click("@start-race-button")
                    ->pause(50)
                    ->waitForText("Your turn")
                    ->keys(".chat-input", ["5"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->keys(".chat-input", ["5"]);

            $third->pause(50)
                    ->waitForText("Your turn")
                    ->keys(".chat-input", ["2"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("1")
                    ->keys(".chat-input", ["8"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p1")
                    ->keys(".chat-input", ["6"]);

            $third->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("pp1")
                    ->keys(".chat-input", ["2"])
                    ->waitForText("Fail!")
                    ->screenshot("pp11")
                    ->assertSee("Fail!");

            $first->pause(50)
                    ->screenshot("2")
                    ->keys(".chat-input", ["6"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p2")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("3")
                    ->keys(".chat-input", ["6"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p3")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("4")
                    ->keys(".chat-input", ["2"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p4")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("5")
                    ->keys(".chat-input", ["2"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p5")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("6")
                    ->keys(".chat-input", ["4"]);

            $second->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("p6")
                    ->keys(".chat-input", ["5"]);

            $first->pause(50)
                    ->waitForText("Your turn")
                    ->screenshot("7")
                    ->keys(".chat-input", ["4"])
                    ->assertSee('Win!');

            $second->pause(50)
                    ->waitForText("Fail!")
                    ->assertSee('Fail!');
         });
     }
}
