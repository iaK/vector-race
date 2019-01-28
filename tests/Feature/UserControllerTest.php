<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
    * @test
    */
    public function a_user_can_update_his_settings()
    {
        $user = create(User::class);
        $this->actingAs($user);

        $this->put("/user/{$user->id}", [
            "username" => "supercoolguy",
            "car_color" => "yellow",
            "trace_color" => "purple",
        ])->assertStatus(200);

        $user = $user->fresh();
        $this->assertEquals("supercoolguy", $user->username);
        $this->assertEquals("yellow", $user->car_color);
        $this->assertEquals("purple", $user->trace_color);
    }
}
