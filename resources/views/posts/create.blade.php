@extends('layouts.app')

@section('title', 'Crear Publicación')
@section('hero-title', 'Crear Nueva Publicación')
@section('hero-subtitle', 'Comparte tus ideas con la comunidad')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Nueva Publicación</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Título</label>
                            <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="Ingresa un título atractivo..." required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="content" class="form-label fw-bold">Contenido</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="8" placeholder="Escribe tu contenido aquí..." required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-create">
                                <i class="fas fa-save me-2"></i>Publicar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection