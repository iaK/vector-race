@extends('layouts.layout')

@section('content')
    <img id="car" src="/img/car.png" style="display: none" alt="">
    <vector-game
        :course="{{ $course }}"
        your-car="{{ auth()->user()->id }}"
        :initial-cars="{{ json_encode($cars) }}"
        initial-state="{{ $race->status }}"
        initial-turn="{{ $race->user_turn_id }}"
        winner="{{ $race->winner_id }}"
    ></vector-game>
@endsection
