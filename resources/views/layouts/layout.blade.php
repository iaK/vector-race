<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cars!</title>
    <link rel="stylesheet" href="{{ mix("main.css") }}">
    @yield('head')
</head>

<body class="h-full">
    <div id="app" class="w-full h-full">
        @yield('content')
    </div>
    <script src="{{ mix("app.js") }}"></script>
    @yield('script')
</body>
</html>
