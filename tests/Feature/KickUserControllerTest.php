<?php

namespace Tests\Feature;

use App\Race;
use App\User;
use Tests\TestCase;
use App\Events\PlayerKicked;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KickUserControllerTest extends TestCase
{
    use RefreshDatabase;
    use HandlesRaces;

    public function setUp()
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        Event::fake();
    }

    /**
    * @test
    */
    public function the_game_host_can_kick_users_before_game_has_started()
    {
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[0]);

        $this->post("race/{$this->race->id}/kick/{$this->users[1]->id}")
            ->assertStatus(200);

        $this->assertCount(1, $this->race->fresh()->participants);
        Event::assertDispatched(PlayerKicked::class);
    }

    /**
    * @test
    */
    public function the_game_host_cant_kick_users_when_game_has_started()
    {
        $this->createRaceWithSatusAndAttachUsers("going", 2);
        $this->actingAs($this->users[0]);

        $this->post("race/{$this->race->id}/kick/{$this->users[1]->id}")
            ->assertStatus(403);

        $this->assertCount(2, $this->race->fresh()->participants);
    }

    /**
    * @test
    */
    public function a_non_host_cant_kick_users()
    {
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[1]);

        $this->post("race/{$this->race->id}/kick/{$this->users[1]->id}")
            ->assertStatus(403);

        $this->assertCount(2, $this->race->fresh()->participants);
    }
}
