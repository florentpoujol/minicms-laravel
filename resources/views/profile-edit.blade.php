<?php
/**
 * @var \App\Models\User $user
 * @var array<\App\Enums\UserRole> $roles
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>
@extends('layout')

@section('title', 'Profile')

@section('body')
    <h1>Edit profile</h1>

    <div>
        @if ($user->hasAdminRole())
            <p>Id: {{ $user->id }}</p>
        @endif

        <form method="post">
            @csrf

            <label for="name">Name: </label> <br>
            <input type="text" id="name" name="name" placeholder="your name" value="{{ $user->name }}"/> <br>
            @include('includes.validation-errors', ['key' => 'name'])
            <br>

            <label for="email">Email:</label> <br>
            <input type="email" id="email" name="email" placeholder="your email" value="{{ $user->email }}"/> <br>
            @include('includes.validation-errors', ['key' => 'email'])
            <br>

            <label for="password">Password:</label> <br>
            <input type="password" id="password" name="password" placeholder="only fill if you want to change your password"/><br>
            @include('includes.validation-errors', ['key' => 'password'])
            <br>

            <label for="password_confirm">Confirm your password:</label> <br>
            <input type="password" id="password_confirm" name="password_confirm"/> <br>
            @include('includes.validation-errors', ['key' => 'password_confirm'])
            <br>

            <label for="role">Role:</label> <br>
            <select
                name="role"
                @if (! $user->hasAdminRole()) disabled @endif>

                @foreach($roles as $role)
                    <option
                        value="{{ $role->value }}"
                        @if ($user->role === $role) selected @endif
                    >{{ $role->value }}</option>
                @endforeach

            </select>
            <br>
            <br>

            <input type="submit" value="Submit">
        </form>
    </div>
@endsection


