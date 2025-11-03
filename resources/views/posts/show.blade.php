@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $post->title }}</h1>
        <p>{{ $post->content }}</p>
        <p><small>Por: {{ $post->user->name }}</small></p>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Volver</a>
    </div>
@endsection
