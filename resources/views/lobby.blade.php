@extends('layouts.layout')

@section('content')
    <result-board></result-board>
    <user-settings></user-settings>
    <how-to></how-to>
    <img id="yellow" src="/img/car_yellow.png" style="display: none" alt="">
    <img id="green" src="/img/car_green.png" style="display: none" alt="">
    <img id="blue" src="/img/car_blue.png" style="display: none" alt="">
    <img id="red" src="/img/car_red.png" style="display: none" alt="">
    <img id="orange" src="/img/car_orange.png" style="display: none" alt="">
    <img id="purple" src="/img/car_purple.png" style="display: none" alt="">
    <div class="container mx-auto">
        <router-view>
    </div>
    <div class="fixed pin-r pin-b p-5 italic text-white text-xs">
        <div>
            Made by Isak Berglind at <a class="text-white" href="http://www.landslide-design.se">Landslide design</a>.
        </div>
        <div>
            Found a bug? message me at <a class="text-white" href="mailto:isak@landslide-design.se">isak@landslide-design.se</a>
        </div>
    </div>
@endsection

@section('head')
    <script>
        DATA = { user: @json(auth()->user()) };
    </script>
@endsection
