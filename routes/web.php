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
    Route::get('/', 'RaceController@index');
    Route::get("/race", 'RaceController@index');
    Route::get("/race/create", "RaceController@create");
    Route::post("/race", "RaceController@store");
    Route::get("/races", "RaceController@races");
    Route::get('/race/{race}', "RaceController@find");
    Route::post("/race/{race}/move", "RaceController@move");
    Route::post("/race/{race}/start", "RaceController@start");
    Route::post("/race/{race}/stop", "RaceController@stop");
    Route::post("/race/{race}/win", "RaceController@win");
    Route::post("/race/{race}/fail", "RaceController@fail");
    Route::post("/race/{race}/skip", "RaceController@skip");
    Route::post("/race/{race}/chat", "RaceController@chat");
    Route::get("/race/{race}/info", "RaceController@info");
    Route::post("/race/{race}/join", "RaceController@join");
    Route::post("/race/{race}/leave", "RaceController@leave");
    Route::post("/race/{race}/kick/{user}", "RaceController@kick");

    Route::put("/user/{user}", "UserController@update");
});


Auth::routes();

Route::fallback(function () {
    return view("lobby");
});
