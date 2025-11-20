<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Notas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8">Sistema de Notas</h1>

        <!-- Mensajes -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Estadísticas -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">
                <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                Estadísticas
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $users->count() }}</div>
                    <div class="text-sm text-blue-800">Usuarios</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    @php
                        $totalNotas = 0;
                        foreach ($users as $user) {
                            $totalNotas += $user->notas->count();
                        }
                    @endphp
                    <div class="text-2xl font-bold text-green-600">{{ $totalNotas }}</div>
                    <div class="text-sm text-green-800">Total Notas</div>
                </div>
            </div>
        </div>

        <!-- Lista de usuarios -->
        <div class="space-y-6">
            @foreach($users as $user)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-xl font-semibold">
                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                {{ $user->name }}
                            </h2>
                            <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            {{ $user->notas->count() }} notas
                        </span>
                    </div>

                    @if($user->notas->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($user->notas as $nota)
                                <div class="border rounded-lg p-4 bg-gray-50">
                                    <h3 class="font-semibold text-lg mb-2">{{ $nota->titulo }}</h3>
                                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($nota->contenido, 100) }}</p>
                                    <div class="text-xs text-gray-500">
                                        Creado: {{ $nota->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay notas para este usuario.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>