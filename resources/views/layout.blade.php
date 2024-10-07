<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Styles -->
        <style>

        </style>
    </head>
    <body>
        <nav>
            <ul>
                <li><a href="{{ route('blog') }}">blog</a></li>

                @guest()
                <li><a href="{{ route('login.show') }}">login</a></li>
                @endguest

                @auth()
                <li><a href="{{ route('profile.show') }}">profile</a></li>
                <li><a href="{{ route('logout') }}">logout</a></li>
                @endauth
            </ul>
        </nav>

        @section('body')

        @show
    </body>
</html>
