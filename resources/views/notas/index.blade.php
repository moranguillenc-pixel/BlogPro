@php
    // SOLUCIÓN: Cargar usuarios si la variable no existe
    if (!isset($users)) {
        $users = \App\Models\User::with(['notas', 'notas.recordatorio'])->get();
    }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Notas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8">Sistema de Notas con Recordatorios</h1>

        <!-- Formulario para crear nueva nota -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Crear Nueva Nota</h2>
            <form action="{{ route('notas.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select name="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Seleccionar usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha Vencimiento</label>
                        <input type="datetime-local" name="fecha_vencimiento" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" name="titulo" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contenido</label>
                    <textarea name="contenido" required rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <button type="submit" 
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Crear Nota
                </button>
            </form>
        </div>

        <!-- Lista de usuarios y sus notas -->
        <div class="space-y-6">
            @foreach($users as $user)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            @php
                                // Calcular notas activas manualmente si no viene del controlador
                                $notasActivas = $user->notas->where('recordatorio.completado', false)
                                                          ->where('recordatorio.fecha_vencimiento', '>=', now())
                                                          ->count();
                            @endphp
                            {{ $user->total_notas ?? $notasActivas }} notas activas
                        </span>
                    </div>
                    
                    @if($user->notas && $user->notas->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($user->notas as $nota)
                                @if($nota->recordatorio)
                                <div class="border rounded-lg p-4 bg-gray-50">
                                    <h3 class="font-semibold text-lg mb-2">
                                        @php
                                            // Formatear título manualmente si el accesor no funciona
                                            $tituloFormateado = $nota->recordatorio->completado 
                                                ? "[Completado] {$nota->titulo}" 
                                                : $nota->titulo;
                                        @endphp
                                        {{ $tituloFormateado }}
                                    </h3>
                                    <p class="text-gray-600 mb-3">{{ $nota->contenido }}</p>
                                    <div class="text-sm text-gray-500">
                                        <strong>Vence:</strong> 
                                        {{ $nota->recordatorio->fecha_vencimiento->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="mt-2">
                                        @if($nota->recordatorio->completado)
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                                Completada
                                            </span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                                Pendiente
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        @if(!$nota->recordatorio->completado)
                                        <form action="{{ route('notas.completar', $nota) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                                Completar
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('notas.destroy', $nota) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600" 
                                                    onclick="return confirm('¿Eliminar esta nota?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay notas activas para este usuario.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Script para mensajes -->
    <script>
        // Mostrar mensajes de éxito
        @if(session('success'))
            alert('{{ session('success') }}');
        @endif

        // Manejar errores de validación
        @if($errors->any())
            alert('Errores en el formulario: {{ $errors->first() }}');
        @endif

        // Debug: mostrar información de usuarios cargados
        console.log('Usuarios cargados:', @json($users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'notas_count' => $user->notas ? $user->notas->count() : 0
            ];
        })));
    </script>
</body>
</html>