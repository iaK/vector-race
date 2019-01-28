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
use Tests\Helpers\HandlesUsers;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LobbyControllerTest extends TestCase
{
    use RefreshDatabase;
    use HandlesUsers;

    public function setUp()
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        Event::fake();
    }

    /**
    * @test
    */
    public function it_can_load_the_lobby_view()
    {
        $this->actingAs(create(User::class));

        $this->get("/lobby")
            ->assertStatus(200)
            ->assertViewIs('lobby');
    }
}
