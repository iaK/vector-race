@extends('layouts.layout')

@section('content')
    <result-board></result-board>
    <user-settings class="lg:block"></user-settings>
    <how-to></how-to>
    <div class="mx-auto">
        <router-view>
    </div>
    <div class="fixed pin-r pin-b p-5 italic text-white text-xs hidden lg:block">
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
