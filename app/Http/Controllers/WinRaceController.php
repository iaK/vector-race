<?php

namespace App\Http\Controllers;

use App\Race;
use App\User;
use App\Events\PlayerWon;
use App\Events\GameClosed;
use App\Events\TurnChanged;
use App\Events\PlayerFailed;
use Illuminate\Http\Request;

class WinRaceController extends Controller
{
     public function store(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $this->moveCar($request, $race);
        $race->crownWinner(auth()->user());

        broadcast(new PlayerWon($race, auth()->user()));

        return $this->respond();
    }

    public function destroy(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $this->moveCar($request, $race);

        $race->failUser(auth()->user(), $request->reason);
        broadcast(new PlayerFailed($race, auth()->user(), $request->reason));

        if ($race->participants->count() > 1) {
            if ($race->fresh()->stillInRace()->count() == 1) {
                $this->crownWinnerIfOneLeft($race->fresh());
            } else {
                $nextUser = $race->changeTurn();
                broadcast(new TurnChanged($race, $nextUser->id));
            }
        } else {
            // if player race by himself
            $race->crownWinner(auth()->user());
        }

        return $this->respond();
    }
}
