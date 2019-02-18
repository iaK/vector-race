<!DOCTYPE html>
<html lang="en" class="h-full overflow-y-scroll">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vectorrace!</title>
    <link rel="stylesheet" href="{{ asset("main.css") }}">
    @yield('head')
</head>

<body class="overflow-scroll lg:overflow-auto">
    <div id="app" class="w-full h-full">
        @yield('content')
    </div>
    <script src="{{ asset("app.js") }}"></script>
    @yield('script')
</body>
</html>
