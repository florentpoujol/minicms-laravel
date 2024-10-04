@php use Illuminate\Support\ViewErrorBag; @endphp
<?php
/**
 * @var ViewErrorBag $errors Set by \Illuminate\View\Middleware\ShareErrorsFromSession
 */
?>
@extends('layout')

@section('title', 'Login')

@section('body')
    <form method="POST" action="{{ route('login.login') }}">
        @csrf

        <label for="email">Email:</label> <br>
        <input type="email" name="email" id="email" required autofocus/>
        @error('email')
        <p style="color: red;">
            {{ $message }}
        </p>
        @enderror
        <br><br>

        <label for="password">Password:</label> <br>
        <input type="password" name="password" id="password"/>
        @error('password')
            <p style="color: red;">
                {{ $message }}
            </p>
        @enderror
        <br><br>

        <label for="remember-me">Remember me:</label>
        <input type="checkbox" name="remember-me" id="remember-me"/>
        <br><br>
        <input type="submit" value="Log In">
    </form>
@endsection
