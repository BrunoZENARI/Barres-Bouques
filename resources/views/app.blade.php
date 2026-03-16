<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" href="{!! asset('favicon.svg') !!}"/>
        <title>Laravel</title>
        <script>window.__ASSET_URL__ = '{{ config('app.BASE_URL') }}';</script>
        @vite('resources/js/app.js')
    </head>
    <body style="margin-bottom: 0px;">

        <div id="app"></div>
        
    </body>
</html>