<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cars!</title>
    <link rel="stylesheet" href="{{ mix("main.css") }}">
</head>
<body style="display:flex;justify-content: center">
    <canvas id="game" height="960" width="960"></canvas>
    <img id="car" src="/img/car.jpg" style="display: none" alt="">
    <script src="{{ mix("main.js") }}"></script>
</body>
</html>
