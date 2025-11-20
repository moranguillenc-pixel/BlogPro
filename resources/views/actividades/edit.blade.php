@extends('layouts.app')

@section('title', 'Editar Actividad')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Editar Actividad</h1>
            <p class="text-gray-600 mt-2">Modifica los datos de la actividad</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('actividades.update', $actividad) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Aviso si no tiene nota -->
                @if(!$actividad->nota)
                    <div class="mb-5 p-4 bg-yellow-50 border border-yellow-300 rounded-md text-yellow-800 text-sm">
                        ℹ️ Esta actividad no tiene nota asociada. Puedes asignarle una ahora.
                    </div>
                @endif

                <!-- Nota (opcional) -->
                <div class="mb-4">
                    <label for="nota_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Nota <span class="text-gray-500">(opcional)</span>
                    </label>
                    <select name="nota_id" id="nota_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Ninguna nota</option>
                        @foreach($notas as $nota)
                            <option value="{{ $nota->id }}" {{ old('nota_id', $actividad->nota_id) == $nota->id ? 'selected' : '' }}>
                                {{ $nota->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Título -->
                <div class="mb-4">
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                    <input type="text" name="titulo" id="titulo" required value="{{ old('titulo', $actividad->titulo) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $actividad->descripcion) }}</textarea>
                </div>

                <!-- Fecha Límite y Prioridad -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="fecha_limite" class="block text-sm font-medium text-gray-700 mb-2">Fecha Límite</label>
                        <input type="date" name="fecha_limite" id="fecha_limite"
                               value="{{ old('fecha_limite', $actividad->fecha_limite?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="prioridad" class="block text-sm font-medium text-gray-700 mb-2">Prioridad *</label>
                        <select name="prioridad" id="prioridad" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona prioridad</option>
                            <option value="3" {{ old('prioridad', $actividad->prioridad) == 3 ? 'selected' : '' }}>Alta</option>
                            <option value="2" {{ old('prioridad', $actividad->prioridad) == 2 ? 'selected' : '' }}>Media</option>
                            <option value="1" {{ old('prioridad', $actividad->prioridad) == 1 ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>
                </div>

                <!-- Completada -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="completada" value="1"
                               {{ old('completada', $actividad->completada) ? 'checked' : '' }}
                               class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Marcar como completada</span>
                    </label>
                </div>

                <!-- BOTONES CON CLASES DE BOOTSTRAP (FUNCIONAN 100% EN TU PROYECTO) -->
                <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                    <a href="{{ route('actividades.index') }}" class="btn btn-secondary btn-lg px-5">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-success btn-lg px-6 fw-bold">
                        <i class="fas fa-save me-2"></i> Actualizar Actividad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection