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
            return $this->index();
        }

        return redirect("/404");
    }

    public function races(Request $request)
    {

    }

    public function join(Race $race)
    {
        if ($race->isInRace(auth()->user())) {
            return $this->info($race->fresh());
        }

        if ($race->participants->count() < 4) {
            $race->addParticipant(auth()->user());
            broadcast(new PlayerJoined($race, auth()->user()));
            return $this->info($race->fresh());
        }

        return response()->json([
            "message" => "Game is full..",
        ], 422);
    }

    public function kick(Race $race, User $user)
    {
        if ($race->host->id !== auth()->id() || $race->status != "lobby") {
            return $this->respond([], 403);
        }

        $race->removeParticipant($user);

        broadCast(new PlayerKicked($race->fresh(), $user));

        return $this->respond();
    }

    public function leave(Race $race)
    {
        if ($race->status == "lobby") {
            if ($race->host_id == auth()->id()) {
                $race->participants()->detach();
                $race->update(["status" => "closed"]);
                broadcast(new GameClosed($race))->toOthers();

                return $this->respond();
            }

            $race->removeParticipant(auth()->user());
            broadcast(new PlayerLeft($race->fresh(), auth()->user()));
        }

        if ($race->status == "going") {
            $race->leaveUser(auth()->user());

            broadcast(new PlayerLeft($race->fresh(), auth()->user()));

            $this->crownWinnerIfOneLeft($race->fresh());
        }


        return $this->respond($race->fresh());
    }

    public function create()
    {
        return $this->index();
    }

    public function info(Race $race)
    {
        return response()->json([
            "id" => $race->id,
            "course" => $race->course,
            "user_id" => auth()->id(),
            "user_turn_id" => $race->user_turn_id,
            "state" => $race->status,
            "participants" => $race->participantsAsJson(),
            "winner_id" => $race->winner_id,
            "host_id" => $race->host_id,
        ]);
    }

    public function start(Request $request, Race $race)
    {
        $race->start();

        broadcast(new RaceStarted($race));
        broadcast(new TurnChanged($race, $race->fresh()->userTurn->id));
        StartCountdown::dispatch($race, $race->moves);
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

    public function stop(Race $race)
    {
        $race->update(["status" => "lobby"]);
    }

    public function skip(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $nextUser = $race->changeTurn();
        broadcast(new TurnChanged($race, $nextUser->id));
    }

    public function move(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $this->moveCar($request, $race);
        $nextUser = $race->changeTurn();

        broadcast(new TurnChanged($race, $nextUser->id));
        StartCountdown::dispatch($race, $race->moves);
    }


    public function win(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $this->moveCar($request, $race);
        $race->crownWinner(auth()->user());

        broadcast(new PlayerWon($race, auth()->user()));
    }

    public function fail(Request $request, Race $race)
    {
        if (! $race->user_turn_id == auth()->id() && $race->status == "going") {
            return;
        }

        $this->moveCar($request, $race);

        $race->failUser(auth()->user(), $request->reason);

        broadcast(new PlayerFailed($race, auth()->user(), $request->reason));

        if ($race->participants->count() > 1) {
            $this->crownWinnerIfOneLeft($race);
        } else {
            $race->crownWinner(auth()->user());
        }
    }

    protected function crownWinnerIfOneLeft($race)
    {
        tap($race->stillInRace(), function($stillInRace) use ($race) {
            if ($stillInRace->count() == 0) {
                $race->update(["status" => "closed"]);
                broadcast(new GameClosed($race))->toOthers();
            }
            if ($stillInRace->count() == 1) {
                $user = User::find($stillInRace[0]["id"]);
                $race->crownWinner($user);
                broadcast(new PlayerWon($race, $user));
            }
        });
    }


    public function chat(Request $request, Race $race)
    {
        broadcast(new MessagePosted($race, $request->message, $request->type))->toOthers();
    }

    protected function moveCar(Request $request, Race $race)
    {
        $race->addMove(auth()->user(), $request->location, $request->speed);
        broadcast(new CarMoved($race, $request->location, $request->id, $request->speed));
    }

    protected function respond($data = [], $statusCode = 200)
    {
        return response()->json([
            "status" => "ok",
            "data" => $data,
        ],$statusCode);
    }

}
