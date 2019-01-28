<?php

namespace Tests\Feature;

use App\Race;
use App\User;
use Tests\TestCase;
use App\Events\PlayerWon;
use App\Events\RaceCreated;
use App\Events\RaceStarted;
use App\Events\TurnChanged;
use App\Events\PlayerFailed;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RaceControllerTest extends TestCase
{
    use RefreshDatabase;
    use HandlesRaces;

    public function setUp()
    {
        parent::setUp();
        Event::fake();
        $this->withoutExceptionHandling();
    }

    /**
    * @test
    */
    public function it_can_create_a_game()
    {
        $this->actingAs(create(User::class));

        $this->post("/race", ["course_id" => 1])->assertStatus(200);

        $this->assertCount(1, Race::all());
        Event::assertDispatched(RaceCreated::class);
    }
}
