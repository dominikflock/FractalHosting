<!DOCTYPE html>
<html lang="de">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{env('APP_NAME')}}</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="/css/bootstrap.min.css">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="/css/fontawesome-type.min.css">
        <link rel="stylesheet" href="/css/fontawesome.min.css">
        <script src="/js/fontawesome.min.js"></script>

        <!-- Flow -->
        <script src="/js/flow.js"></script>

        <!-- App -->
        <script src="/js/app.js"></script>
    </head>

    <body>
        @yield('content')
    </body>

</html>
