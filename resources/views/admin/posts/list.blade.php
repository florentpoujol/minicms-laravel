<?php
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

/**
 * @var Collection<Post> $posts
 */
?>
@extends('layout')

@section('title', 'Admin home')

@section('body')
    <h1>Posts</h1>

    <table>
        <tr>
            <th>Id</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Content length</th>
            <th>Published at</th>
            <th>Author</th>
            <th>Created at</th>
            <th>Updated at</th>

        </tr>
        @foreach($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->slug }}</td>
                <td>{{ mb_strlen($post->content) }}</td>
                <td>{{ $post->published_at?->toDateTimeString() }}</td>
                <td>{{ $post->author->name }}</td>
                <td>{{ $post->created_at->toDateTimeString() }}</td>
                <td>{{ $post->updated_at->toDateTimeString() }}</td>
            </tr>
        @endforeach
    </table>
@endsection
