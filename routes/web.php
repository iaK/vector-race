<?php


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(["middleware" => "auth"], function () {
    Route::redirect('/', '/lobby');

    Route::put("/user/{user}", "UserController@update");
    Route::get("/lobby", 'LobbyController@index');

    Route::group(["prefix" => "race"], function() {

        Route::post("{race}/join", "JoinRaceController@store");
        Route::delete("/{race}/join", "JoinRaceController@delete");

        Route::get("/{race}/info", "RaceInformationController@index");

        Route::post("/{race}/start", "StartRaceController@post");
        // Route::post("/{race}/stop", "RaceStateController@destroy");

        Route::post("/{race}/move", "MoveCarController@store");

        Route::post("/{race}/win", "WinRaceController@store");
        Route::delete("/{race}/win", "WinRaceController@destroy");

        Route::post("/{race}/skip", "SkipTurnController@store");

        Route::post("/{race}/chat", "ChatController@chat");

        Route::post("/{race}/kick/{user}", "KickUserController@store");

        Route::get('/{race}', "RaceController@find");
        Route::get("/", "RaceController@index");
        Route::post("/", "RaceController@store");
        Route::get("/create", "RaceController@index");
        
    });
});


Auth::routes();

Route::fallback(function () {
    return view("lobby");
});
