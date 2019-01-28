<?php

namespace Tests\Feature;

use App\Race;
use App\User;
use Tests\TestCase;
use App\Events\RaceStarted;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StartRaceControllerTest extends TestCase
{
    use HandlesRaces;
    use RefreshDatabase;
    /**
    * @test
    */
    public function it_can_start_a_race()
    {
        Event::fake();
        $this->createRaceAndAttachUsers(2);
        $this->actingAs($this->users[0]);

        $this->post("/race/{$this->race->id}/start")
            ->assertStatus(200);

        $this->assertEquals("going", $this->race->fresh()->status);
        Event::assertDispatched(RaceStarted::class);
    }

}
