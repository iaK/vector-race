<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Events\TurnChanged;
use App\Jobs\StartCountdown;
use Tests\Helpers\HandlesRaces;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoveCarControllerTest extends TestCase
{
    use RefreshDatabase;
    use HandlesRaces;

    public function setUp()
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        Event::fake();
        Queue::fake();
    }
    /**
    * @test
    */
    public function it_can_move_a_car()
    {
        $this->createRaceAndAttachUsers(2);

        $this->actingAs($this->users[0]);

        $this->post(
            "/race/{$this->race->id}/move",
            ["location" => ["x" => 0, "y" => 0]]
        )->assertStatus(200);

        $this->assertEquals(
            ["x" => 0, "y" => 0],
            $this->race->fresh()->participant_data[0]["trace"][1]
        );
        $this->assertEquals(
            $this->users[1]->id,
            $this->race->fresh()->userTurn->id
        );
        Event::assertDispatched(TurnChanged::class);
        Queue::assertPushed(StartCountdown::class);
    }
}
