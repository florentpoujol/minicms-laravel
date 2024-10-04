@extends('layout')

@section('title', $title)

@section('body')
    <article>
        <h2>{{ $post->title }}</h2>
        <aside>Written by {{ $post->author->name }} on {{ $post->published_at }}</aside>

        <p>
            {{ $post->content }}
        </p>
    </article>
@endsection


