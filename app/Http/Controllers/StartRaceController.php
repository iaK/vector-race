<?php

namespace App\Http\Controllers;

use App\Race;
use App\Events\RaceStarted;
use App\Events\TurnChanged;
use App\Jobs\StartCountdown;
use Illuminate\Http\Request;

class StartRaceController extends Controller
{
    public function post(Request $request, Race $race)
    {
        $race->start();

        broadcast(new RaceStarted($race));
        broadcast(new TurnChanged($race, $race->fresh()->userTurn->id));
        StartCountdown::dispatch($race, $race->moves);

        return $this->respond();
    }
}
