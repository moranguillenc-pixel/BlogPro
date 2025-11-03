@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Publicaciones</h1>
        <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Crear Publicación</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @foreach ($posts as $post)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $post->title }}</h5>
                    <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                    <p class="card-text"><small>Por: {{ $post->user->name }}</small></p>
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-info">Ver</a>
                    @if ($post->user_id === Auth::id())
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Seguro?')">Eliminar</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

