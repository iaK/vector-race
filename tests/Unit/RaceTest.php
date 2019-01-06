<?php

namespace Tests\Unit;

use App\Race;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RaceTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function users_can_join_a_race()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);

        $this->assertCount(2, $race->fresh()->participants);
        $this->assertCount(2, $race->fresh()->participant_data);
    }

    /**
    * @test
    */
    public function it_can_start_a_race()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);

        $race->start();

        $this->assertEquals("going", $race->status);
        $this->assertEquals($race->user_turn_id, $users[0]->id);
    }

    /**
    * @test
    */
    public function it_can_get_the_user_which_turn_it_is()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();
        $this->assertEquals($users[0]->id, $race->userTurn->id);
    }

    /**
    * @test
    */
    public function a_user_can_fail_a_race()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();

        $race->failUser($users[0], "out of bounds");

        $this->assertEquals("failed", $race->participant_data[0]["status"]);
        $this->assertEquals("going", $race->participant_data[1]["status"]);
    }

    /**
    * @test
    */
    public function a_user_can_win_a_race()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();

        $race->crownWinner($users[0]);

        $this->assertEquals($race->winner->id, $users[0]->id);
        $this->assertEquals("ended", $race->fresh()->status);
    }

    /**
    * @test
    */
    public function it_can_remove_a_user()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->removeParticipant($users[0]);

        $this->assertCount(1, $race->participants);
        $this->assertEquals($users[1]->id, $race->participants[0]->id);
        $this->assertCount(1, $race->participant_data);
        $this->assertEquals($users[1]->id, $race->participant_data[0]["id"]);
    }

    /**
    * @test
    */
    public function it_can_register_a_move()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();
        $race->addMove($users[0], ["x" => 0, "y" => 0], ["left" => -5,"top" =>-1]);
        $race->addMove($users[0], ["x" => 1, "y" => 1], ["left" => -5,"top" =>-1]);

        $this->assertEquals(["x" => 0, "y" => 0], $race->participant_data[0]["trace"][1]);
        $this->assertEquals(["x" => 1, "y" => 1], $race->participant_data[0]["trace"][2]);
        $this->assertEquals(["left" => -5,"top" =>-1], $race->participant_data[0]["speed"]);

        $this->assertEquals(2, $race->moves);
    }

    /**
    * @test
    */
    public function it_can_change_turn()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();

        $nextUser = $race->fresh()->changeTurn();
        $this->assertEquals($users[1]->id, $nextUser->id);
        $nextUser = $race->fresh()->changeTurn();
        $this->assertEquals($users[0]->id, $nextUser->id);
    }

    /**
    * @test
    */
    public function a_user_can_leave_a_game()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();

        $race->leaveUser($users[0], "out of bounds");

        $this->assertEquals("left", $race->participant_data[0]["status"]);
        $this->assertEquals("going", $race->participant_data[1]["status"]);
    }

    /**
    * @test
    */
    public function it_can_kick_a_user()
    {
        $users = factory(User::class, 2)->create();
        $race = factory(Race::class)->create();

        $race->addParticipants($users);
        $race->start();

        $race->kickUser($users[0]);

        $this->assertEquals("kicked", $race->participant_data[0]["status"]);
        $this->assertEquals("going", $race->participant_data[1]["status"]);
    }
}
