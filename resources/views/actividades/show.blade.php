@extends('layouts.app')

@section('title', $actividad->titulo)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $actividad->titulo }}</h1>
                <p class="text-gray-600 mt-2">Detalles de la actividad</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('actividades.edit', $actividad) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('actividades.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Información de la Actividad -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Estado y Prioridad -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Estado:</span>
                        @if($actividad->completada)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Completada</span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Pendiente</span>
                        @endif
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Prioridad:</span>
                        <span class="px-3 py-1 {{ $actividad->clase_prioridad }} rounded-full text-sm font-medium">
                            {{ $actividad->texto_prioridad }}
                        </span>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nota Relacionada -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Nota</h3>
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <a href="{{ route('notas.edit', $actividad->nota) }}" class="hover:text-blue-600">
                                {{ $actividad->nota->titulo }}
                            </a>
                        </div>
                    </div>

                    <!-- Fecha Límite -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Fecha Límite</h3>
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            @if($actividad->fecha_limite)
                                {{ $actividad->fecha_limite->format('d/m/Y') }}
                                @if($actividad->esta_vencida && !$actividad->completada)
                                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Vencida</span>
                                @endif
                            @else
                                <span class="text-gray-400">Sin fecha límite</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Descripción -->
                @if($actividad->descripcion)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Descripción</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-line">{{ $actividad->descripcion }}</p>
                        </div>
                    </div>
                @endif

                <!-- Fechas de Creación y Actualización -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <div>
                            <span class="font-medium">Creada:</span> 
                            {{ $actividad->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Actualizada:</span> 
                            {{ $actividad->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="mt-6 flex justify-between items-center">
            <div class="flex space-x-2">
                <!-- Botón Completar/Pendiente -->
                @if($actividad->completada)
                    <form action="{{ route('actividades.pendiente', $actividad) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Marcar como Pendiente
                        </button>
                    </form>
                @else
                    <form action="{{ route('actividades.completar', $actividad) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Marcar como Completada
                        </button>
                    </form>
                @endif
            </div>

            <!-- Botón Eliminar -->
            <form action="{{ route('actividades.destroy', $actividad) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center" onclick="return confirm('¿Estás seguro de eliminar esta actividad?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection