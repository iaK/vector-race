<?php

namespace App\Http\Controllers;

use App\Race;
use App\Events\TurnChanged;
use Illuminate\Http\Request;

class SkipTurnController extends Controller
{

    public function store(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $nextUser = $race->changeTurn();
        broadcast(new TurnChanged($race, $nextUser->id));

        return $this->respond();
    }

}
