<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('race.{raceId}', function ($user, $raceId) {
    if (! $raceId) {
        return;
    }

    $race = App\Race::find($raceId);

    if ($race && $race->isInRace($user)) {
        return $user;
    }
    //$user->withRaceData(Race::find($raceId));//$user->isInRace(App\Race::findOrFail($raceId));
});
