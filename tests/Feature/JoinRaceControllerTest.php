<?php

namespace Tests\Feature;

use App\Race;
use App\User;
use App\Events\GameClosed;
use App\Events\PlayerLeft;
use App\Events\PlayerJoined;
use Tests\TestCase;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JoinRaceControllerTest extends TestCase
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
    public function a_user_can_join_a_game()
    {
        $this->race = create(Race::class);
        $this->actingAs(create(User::class));

        $this->post("/race/{$this->race->id}/join")
            ->assertStatus(201);

        $this->assertCount(1, $this->race->fresh()->participants);
        Event::assertDispatched(PlayerJoined::class);
    }

    /**
    * @test
    */
    public function maximum_four_users_can_join_a_game()
    {
        $this->createRaceAndAttachUsers(4);
        $user = create(User::class);
        $this->actingAs($user);

        $this->post("/race/{$this->race->id}/join")
            ->assertStatus(422);

        $this->assertFalse($this->race->isInRace($user));
        $this->assertCount(4, $this->race->fresh()->participants);
    }

    /**
    * @test
    */
    public function a_user_cant_join_twice()
    {
        $this->createRaceAndAttachUsers(1);
        $this->actingAs($this->users[0]);

        $this->post("/race/{$this->race->id}/join")
            ->assertStatus(200);

        $this->assertTrue($this->race->isInRace($this->users[0]));
        $this->assertCount(1, $this->race->fresh()->participants);
    }

    /**
    * @test
    */
    public function if_the_host_leaves_before_the_game_starts_its_closed()
    {
        $this->createRaceAndAttachUsers(1);
        $this->actingAs($this->users[0]);
        $this->race->update(["host_id" => $this->users[0]->id]);

        $this->delete("/race/{$this->race->id}/join")
            ->assertStatus(200);

        $this->assertEquals("closed", $this->race->fresh()->status);
        Event::assertDispatched(GameClosed::class);
    }

    /**
    * @test
    */
    public function if_a_user_leaves_before_game_starts_he_is_removed()
    {
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[1]);

        $this->delete("/race/{$this->race->id}/join")
            ->assertStatus(200);

        $this->assertFalse($this->race->isInRace($this->users[1]));
        $this->assertCount(1, $this->race->fresh()->participants);
        Event::assertDispatched(PlayerLeft::class);
    }

    /**
    * @test
    */
    public function it_a_user_leaves_after_game_starts_he_is_marked_as_left()
    {
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[0]);
        $this->race->start();

        $this->delete("/race/{$this->race->id}/join")
            ->assertStatus(200);

        $this->assertTrue($this->race->isInRace($this->users[0]));
        $this->assertCount(2, $this->race->fresh()->participants);
        $this->assertEquals("left", $this->race->fresh()->participant_data[0]["status"]);
        Event::assertDispatched(PlayerLeft::class);
    }
}
