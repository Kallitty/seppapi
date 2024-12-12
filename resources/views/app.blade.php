
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <title>Laravel React Project</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div id="root"></div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
