<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\HandlesRaces;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RaceInformationControllerTest extends TestCase
{
    use HandlesRaces;
    use RefreshDatabase;

    /**
    * @test
    */
    public function it_can_get_information_about_a_race()
    {
        $this->withoutExceptionHandling();
        $this->createRaceAndAttachUsers(1);
        $this->actingAs($this->users[0]);

        $response = $this->get("/race/{$this->race->id}/info")
            ->assertStatus(200)
            ->assertJsonStructure(["data" => [
                "id",
                "course",
                "user_turn_id",
                "state",
                "participants",
                "winner_id",
                "host_id",
            ]]);
    }
}
