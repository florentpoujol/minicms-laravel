<?php
/**
 * @var \App\Models\User $user
 */
?>
@extends('layout')

@section('title', 'Profile')

@section('body')
    <h1>Profile</h1>

    <div>
        <ul>
            @if ($user->hasAdminRole())
                <li>Id: {{ $user->id }}</li>
            @endif
            <li>Name: {{ $user->name }}</li>
            <li>Email: {{ $user->email }}</li>
            <li>Role: {{ $user->role->value }}</li>
            <li>Registered at: {{ $user->created_at }}</li>

            @if ($user->email_verified_at !== null)
                <li>Email verified</li>
            @else
                <li>Email not verified. <a href="">Resend the link</a></li>
            @endif
        </ul>

        <ul>
            <li><a href="{{ route('profile.show_edit') }}">Edit profile</a></li>
            <li><a href="{{ route('logout') }}">Logout</a></li>
        </ul>


    </div>
@endsection


