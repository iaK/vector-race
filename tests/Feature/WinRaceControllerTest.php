<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Events\PlayerWon;
use App\Events\PlayerFailed;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WinRaceControllerTest extends TestCase
{
    use HandlesRaces;
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        Event::fake();
        $this->withoutExceptionHandling();
    }

    /**
    * @test
    */
    public function a_user_can_win()
    {
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[0]);
        $this->race->start();

        $this->post("/race/{$this->race->id}/win")
            ->assertStatus(200);

        $this->assertEquals($this->users[0]->id, $this->race->fresh()->winner->id);
        $this->assertEquals("ended", $this->race->fresh()->status);
        Event::assertDispatched(PlayerWon::class);
    }


    /**
    * @test
    */
    public function a_user_can_fail()
    {
        $this->createRaceAndAttachUsers(3);
        $this->race->start();
        $this->actingAs($this->users[0]);

        $this->delete("/race/{$this->race->id}/win")
            ->assertStatus(200);

        $this->assertEquals("failed", $this->race->fresh()->participant_data[0]["status"]);
        Event::assertDispatched(PlayerFailed::class);
    }

    /**
    * @test
    */
    public function it_a_user_fails_the_turn_goes_over()
    {
        $this->createRaceAndAttachUsers(3);
        $this->race->start();
        $this->actingAs($this->users[0]);

        $this->delete("/race/{$this->race->id}/win")
            ->assertStatus(200);

        $this->assertEquals("failed", $this->race->fresh()->participant_data[0]["status"]);
        $this->assertEquals($this->users[1]->id, $this->race->fresh()->user_turn_id);
        Event::assertDispatched(PlayerFailed::class);
    }


    /**
    * @test
    */
    public function if_all_users_fail_except_one_that_player_wins()
    {
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[0]);
        $this->race->start();

        $this->delete("/race/{$this->race->id}/win")
            ->assertStatus(200);

        $this->assertEquals($this->users[1]->id, $this->race->fresh()->winner->id);
        $this->assertEquals("ended", $this->race->fresh()->status);
        Event::assertDispatched(PlayerFailed::class);
        Event::assertDispatched(PlayerWon::class);
    }
}
