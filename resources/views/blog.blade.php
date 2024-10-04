@php use Illuminate\Support\Str; @endphp
@extends('layout')

@section('title', 'Blog')

@section('body')
    <h1>Blog</h1>

    <h2>Recent articles</h2>

    @foreach($posts as $post)
        <article>
            <h3>{{ $post->title }}</h3>
            <aside>Written by {{ $post->author->name }} on {{ $post->published_at }}</aside>

            <p>
                {{ Str::limit($post->content, 300) }}
            </p>

            <p>
                <a href="{{ route('post.show', ['slug' => $post->slug]) }}">Read more</a>
            </p>
        </article>
    @endforeach
@endsection


