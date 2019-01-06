@extends('layouts.layout')

@section('content')
    <result-board></result-board>
    <user-settings></user-settings>
    <how-to></how-to>
    <img id="car" src="/img/car.png" style="display: none" alt="">
    <div class="container mx-auto">
        <router-view>
    </div>
    <div class="fixed pin-r pin-b p-5 italic text-white">
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
