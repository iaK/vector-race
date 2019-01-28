<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkipTurnControllerTest extends TestCase
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
    public function a_user_can_skip_his_turn()
    {
        $this->createRaceAndAttachUsers(2);
        $this->race->start();
        $this->actingAs($this->users[0]);

        $this->post("/race/{$this->race->id}/skip")
            ->assertStatus(200);

        $this->assertEquals($this->users[1]->id, $this->race->fresh()->user_turn_id);
    }
}
