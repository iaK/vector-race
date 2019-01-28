<?php

namespace App\Http\Controllers;

use App\Race;
use App\User;
use App\Events\GameClosed;
use App\Events\PlayerLeft;
use App\Events\PlayerJoined;
use Illuminate\Http\Request;

class JoinRaceController extends Controller
{
    public function store(Race $race)
    {
        $user = auth()->user();

        if ($race->isInRace($user)) {
            return $this->respond($race->toArray(), 200);
        }

        if ($race->participants->count() < 4) {
            $race->addParticipant($user);
            broadcast(new PlayerJoined($race, $user));
            return $this->respond($race->fresh()->toArray(), 201);
        }

        return $this->respondWithError("Game is full", 422);
    }

    public function delete(Race $race)
    {
        if ($race->status == "lobby") {
            $this->removeUserFromLobby($race, auth()->user());
        }

        if ($race->status == "going") {
            $this->removeUserFromOngoingRace($race, auth()->user());
        }

        return $this->respond($race->fresh());
    }

    protected function closeRace(Race $race)
    {
        $race->participants()->detach();
        $race->update(["status" => "closed"]);

        broadcast(new GameClosed($race))->toOthers();

        return $this->respond();
    }

    protected function removeUserFromLobby(Race $race, User $user)
    {
        if ($race->isHost($user)) {
            return $this->closeRace($race);
        }

        $race->removeParticipant($user);

        broadcast(new PlayerLeft($race->fresh(), $user));
    }

    protected function removeUserFromOngoingRace(Race $race, User $user)
    {
        $race->leaveUser($user);

        broadcast(new PlayerLeft($race->fresh(), $user));

        $this->crownWinnerIfOneLeft($race->fresh());
    }
}
