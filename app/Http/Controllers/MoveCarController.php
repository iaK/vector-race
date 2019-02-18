<?php

namespace App\Http\Controllers;

use App\Race;
use App\Events\TurnChanged;
use App\Jobs\StartCountdown;
use Illuminate\Http\Request;

class MoveCarController extends Controller
{
    public function store(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $this->moveCar($request, $race);

        $nextUser = $race->changeTurn();
        broadcast(new TurnChanged($race, $nextUser->id));

        StartCountdown::dispatch($race, $race->moves);

        return $this->respond();
    }
}
