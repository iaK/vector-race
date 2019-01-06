<?php

namespace Tests\Feature;

use App\Race;
use App\User;
use Tests\TestCase;
use App\Events\PlayerWon;
use App\Events\TurnChanged;
use App\Events\RaceStarted;
use App\Events\RaceCreated;
use App\Events\PlayerFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RaceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        Event::fake();
    }


    /**
    * @test
    */
    public function it_can_start_a_race()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);

        $this->actingAs($users[0]);

        $this->post("/race/{$race->id}/start")->assertStatus(200);

        $race = $race->fresh();
        $this->assertCount(2, $race->participants);
        $this->assertCount(2, $race->participant_data);
        $this->assertEquals("going", $race->status);

        Event::assertDispatched(RaceStarted::class);
    }

    /**
    * @test
    */
    public function it_can_move_a_car()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);
        $race->start();

        $this->actingAs($users[0]);

        $this->post(
            "/race/{$race->id}/move",
            ["location" => ["x" => 0, "y" => 0]]
        )->assertStatus(200);

        $this->assertEquals(
            ["x" => 0, "y" => 0],
            $race->fresh()->participant_data[0]["trace"][1]
        );
        $this->assertEquals($users[1]->id, $race->fresh()->userTurn->id);
        Event::assertDispatched(TurnChanged::class);
    }

    /**
    * @test
    */
    public function a_user_can_win()
    {
        $this->withoutExceptionHandling();
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);
        $race->start();

        $this->actingAs($users[0]);

        $this->post("/race/{$race->id}/win")
            ->assertStatus(200);

        $this->assertEquals($users[0]->id, $race->fresh()->winner->id);
        $this->assertEquals("ended", $race->fresh()->status);
        Event::assertDispatched(PlayerWon::class);
    }

    /**
    * @test
    */
    public function a_user_can_fail()
    {
        $users = factory(User::class, 3)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);
        $race->start();

        $this->actingAs($users[0]);

        $this->post("/race/{$race->id}/fail")
            ->assertStatus(200);

        $this->assertEquals("failed", $race->fresh()->participant_data[0]["status"]);
        Event::assertDispatched(PlayerFailed::class);
    }

    /**
    * @test
    */
    public function if_all_users_fail_except_one_that_player_wins()
    {
        $this->withoutExceptionHandling();
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);
        $race->start();

        $this->actingAs($users[0]);

        $this->post("/race/{$race->id}/fail")
            ->assertStatus(200);

        $this->assertEquals($users[1]->id, $race->fresh()->winner->id);
        $this->assertEquals("ended", $race->fresh()->status);
        Event::assertDispatched(PlayerFailed::class);
        Event::assertDispatched(PlayerWon::class);
    }

    /**
    * @test
    */
    public function if_a_player_leaves_before_game_starts_he_is_removed()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);

        $this->actingAs($users[0]);

        $this->post("/race/{$race->id}/leave")
            ->assertStatus(200);

        $this->assertCount(1, $race->fresh()->participants);
    }

    /**
    * @test
    */
    public function it_a_player_leaves_after_game_starts_he_is_marked_as_left()
    {
        $this->withoutExceptionHandling();
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();
        $race->addParticipants($users);
        $race->start();

        $this->actingAs($users[0]);

        $this->post("/race/{$race->id}/leave")
            ->assertStatus(200);

        $this->assertCount(2, $race->fresh()->participants);
        $this->assertEquals("left", $race->fresh()->participant_data[0]["status"]);
    }

    /**
    * @test
    */
    public function it_can_create_a_game()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $this->post("/race", ["course_id" => 1])->assertStatus(200);

        $this->assertCount(1, Race::all());
        Event::assertDispatched(RaceCreated::class);
    }

    /**
    * @test
    */
    public function the_game_host_can_kick_users_before_game_has_started()
    {
        $this->withoutExceptionHandling();
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create(["host_id" => $users[0]->id]);

        $race->addParticipants($users);
        $this->actingAs($users[0]);

        $this->post("race/{$race->id}/kick/{$users[1]->id}")
            ->assertStatus(200);

        $this->assertCount(1, $race->fresh()->participants);
    }

    /**
    * @test
    */
    public function the_game_host_cant_kick_users_when_game_has_started()
    {
        $this->withoutExceptionHandling();
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create([
            "host_id" => $users[0]->id,
            "status" => "going"
        ]);

        $race->addParticipants($users);
        $this->actingAs($users[0]);

        $this->post("race/{$race->id}/kick/{$users[1]->id}")
            ->assertStatus(403);

        $this->assertCount(2, $race->fresh()->participants);
    }
}
