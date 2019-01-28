<?php

namespace App\Http\Controllers;

use App\Race;
use App\User;
use App\Events\CarMoved;
use App\Events\PlayerWon;
use App\Events\RaceStarted;
use App\Events\RaceCreated;
use App\Events\TurnChanged;
use App\Events\PlayerFailed;
use App\Events\PlayerJoined;
use App\Events\PlayerKicked;
use App\Events\GameClosed;
use App\Events\PlayerLeft;
use App\Events\MessagePosted;
use App\Jobs\StartCountdown;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    public function index()
    {
        $races = Race::with(["host", "participants"])
            ->join('race_user', function ($join) {
                $join->on('race_user.race_id', '=', 'races.id')
                    ->where("race_user.user_id", "=", auth()->id());
            })
            ->whereStatus('lobby')
            ->orWhere(function ($query) {
                $query->where('status', "going")
                      ->whereRaw('race_user.user_id IS NOT NULL');
            })->get();

        return $this->respond($races);
    }

    public function find(Request $request, Race $race)
    {
        if (in_array($race->status, ["lobby", "going"])) {
            return view('lobby');
        }

        return redirect("/404");
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasUnfinishedRaces()) {
            return $this->respond([
                "message" => "You can't create a game when you have unfinished games"
            ], 403);
        }

        $race = Race::create([
            "host_id" => auth()->id(),
            "course_id" => $request->course_id,
            "status" => "lobby",
            "participant_data" => [],
        ]);

        broadcast(new RaceCreated($race));

        return $this->respond($race);
    }
}
