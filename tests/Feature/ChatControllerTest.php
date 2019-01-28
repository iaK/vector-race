<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\HandlesRaces;
use App\Events\MessagePosted;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatControllerTest extends TestCase
{
    use HandlesRaces;
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        Event::fake();
    }

    /**
    * @test
    */
    public function a_user_can_post_a_message()
    {
        $this->createRaceAndAttachUsers(1);

        $this->actingAs($this->users[0]);

        $this->post("/race/{$this->race->id}/chat", [
            "message" => "hey!",
            "type" => "message"
        ]);

        Event::assertDispatched(MessagePosted::class);
    }
}
