<?php

namespace App\Http\Controllers;

use App\Race;
use App\User;
use App\Events\PlayerWon;
use App\Events\CarMoved;
use App\Events\GameClosed;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function moveCar(Request $request, Race $race)
    {
        $race->addMove(auth()->user(), $request->location, $request->speed);
        broadcast(new CarMoved($race, $request->location, $request->id, $request->speed));
    }

    protected function crownWinnerIfOneLeft(Race $race)
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

    protected function respond($data = [], $statusCode = 200)
    {
        return response()->json([
            "status" => "ok",
            "data" => $data,
        ],$statusCode);
    }

    protected function respondWithError($message, $statusCode = 403)
    {
        return response()->json([
            "status" => "error",
            "message" => $message,
        ],$statusCode);
    }

}
