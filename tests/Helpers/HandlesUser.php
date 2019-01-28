<?php

namespace Tests\Helpers;

use App\User;

trait HandlesUsers
{

    public function createAndLogin($attributes = []) {
        $user = factory(User::class)->create($attributes);

        $this->actingAs($user);

        return $user;
    }

}
