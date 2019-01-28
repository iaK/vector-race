<?php

namespace App\Http\Controllers;

use App\Race;
use App\User;
use App\Events\PlayerKicked;
use Illuminate\Http\Request;

class KickUserController extends Controller
{
    public function store(Race $race, User $user)
    {
        if ($race->host->id !== auth()->id() || $race->status != "lobby") {
            return $this->respond([], 403);
        }

        $race->removeParticipant($user);

        broadCast(new PlayerKicked($race->fresh(), $user));

        return $this->respond();
    }

}
