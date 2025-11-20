<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Recordatorio;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    /**
     * Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            $notas = Nota::where('user_id', $user->id)
                        ->with(['actividades', 'recordatorios'])
                        ->withCount(['actividades', 'recordatorios'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

            // Calcular estadísticas de manera más eficiente
            $totalNotas = $notas->total(); // Total de notas paginadas
            $totalActividades = Actividad::whereHas('nota', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            $actividadesCompletadas = Actividad::whereHas('nota', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('completada', true)->count();

            $totalRecordatorios = Recordatorio::whereHas('nota', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            return view('notas.index', compact(
                'notas',
                'totalNotas',
                'totalActividades',
                'actividadesCompletadas',
                'totalRecordatorios'
            ));

        } catch (\Exception $e) {
            Log::error("Error en NotaController@index: " . $e->getMessage());
            
            return redirect()->route('home')
                ->with('error', 'Error al cargar las notas: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'contenido' => 'required|string',
            ]);

            $nota = Nota::create([
                'titulo' => $request->titulo,
                'contenido' => $request->contenido,
                'user_id' => Auth::id(),
            ]);

            Log::info("Nota creada exitosamente - ID: {$nota->id}");

            return redirect()->route('notas.index')
                ->with('success', 'Nota creada exitosamente.');

        } catch (\Exception $e) {
            Log::error("Error al crear nota: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear la nota: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Nota $nota)
    {
        try {
            // Verificar que la nota pertenece al usuario autenticado
            $this->authorize('view', $nota);

            // Cargar relaciones para la vista show
            $nota->load(['actividades', 'recordatorios']);

            // Calcular estadísticas de la nota
            $estadisticas = [
                'total_actividades' => $nota->actividades->count(),
                'actividades_completadas' => $nota->actividades->where('completada', true)->count(),
                'total_recordatorios' => $nota->recordatorios->count(),
            ];

            return view('notas.show', compact('nota', 'estadisticas'));

        } catch (\Exception $e) {
            Log::error("Error al mostrar nota ID: {$nota->id} - Error: " . $e->getMessage());
            return redirect()->route('notas.index')
                ->with('error', 'Error al cargar la nota: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nota $nota)
    {
        try {
            // Verificar que la nota pertenece al usuario autenticado
            $this->authorize('update', $nota);

            return view('notas.edit', compact('nota'));

        } catch (\Exception $e) {
            Log::error("Error al cargar edición de nota ID: {$nota->id} - Error: " . $e->getMessage());
            return redirect()->route('notas.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nota $nota)
    {
        try {
            // Verificar que la nota pertenece al usuario autenticado
            $this->authorize('update', $nota);

            $request->validate([
                'titulo' => 'required|string|max:255',
                'contenido' => 'required|string',
            ]);

            $nota->update([
                'titulo' => $request->titulo,
                'contenido' => $request->contenido,
            ]);

            Log::info("Nota actualizada exitosamente - ID: {$nota->id}");

            return redirect()->route('notas.index')
                ->with('success', 'Nota actualizada exitosamente.');

        } catch (\Exception $e) {
            Log::error("Error al actualizar nota ID: {$nota->id} - Error: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la nota: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * VERSIÓN MEJORADA: Eliminación rápida y segura
     */
    public function destroy(Nota $nota)
    {
        try {
            $user = Auth::user();
            Log::info("Usuario {$user->id} eliminando nota {$nota->id}");

            // Verificar que la nota pertenece al usuario autenticado
            $this->authorize('delete', $nota);

            // Contar relaciones antes de eliminar
            $actividadesCount = $nota->actividades()->count();
            $recordatoriosCount = $nota->recordatorios()->count();

            // Eliminar la nota (la eliminación en cascada se maneja automáticamente)
            $nota->delete();

            Log::info("Nota eliminada - ID: {$nota->id}, Actividades: {$actividadesCount}, Recordatorios: {$recordatoriosCount}");

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nota eliminada exitosamente.',
                    'redirect' => route('notas.index')
                ]);
            }

            return redirect()->route('notas.index')
                ->with('success', 'Nota eliminada exitosamente.');

        } catch (\Exception $e) {
            Log::error("ERROR eliminando nota: " . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al eliminar la nota: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('notas.index')
                ->with('error', 'Error al eliminar la nota: ' . $e->getMessage());
        }
    }

    /**
     * ELIMINACIÓN RÁPIDA - Sin confirmación (para uso interno)
     */
    public function eliminarRapido($id)
    {
        try {
            $user = Auth::user();
            
            // Buscar la nota del usuario actual
            $nota = Nota::where('user_id', $user->id)->findOrFail($id);

            Log::info("ELIMINACIÓN RÁPIDA - Usuario {$user->id} eliminando nota {$nota->id}");

            // Eliminar directamente sin verificación adicional
            $nota->delete();

            Log::info("Nota eliminada rápidamente - ID: {$nota->id}");

            return redirect()->route('notas.index')
                ->with('success', 'Nota eliminada exitosamente.');

        } catch (\Exception $e) {
            Log::error("Error en eliminación rápida: " . $e->getMessage());
            return redirect()->route('notas.index')
                ->with('error', 'Error al eliminar la nota: ' . $e->getMessage());
        }
    }

    /**
     * ELIMINACIÓN MÚLTIPLE - Eliminar varias notas a la vez
     */
    public function eliminarMultiple(Request $request)
    {
        try {
            $user = Auth::user();
            $notasIds = $request->input('notas_ids', []);

            if (empty($notasIds)) {
                return redirect()->route('notas.index')
                    ->with('error', 'No se seleccionaron notas para eliminar.');
            }

            // Verificar que todas las notas pertenecen al usuario
            $notas = Nota::where('user_id', $user->id)
                        ->whereIn('id', $notasIds)
                        ->get();

            $eliminadas = 0;
            foreach ($notas as $nota) {
                $nota->delete();
                $eliminadas++;
            }

            Log::info("Eliminación múltiple - Usuario {$user->id} eliminó {$eliminadas} notas");

            return redirect()->route('notas.index')
                ->with('success', "{$eliminadas} notas eliminadas exitosamente.");

        } catch (\Exception $e) {
            Log::error("Error en eliminación múltiple: " . $e->getMessage());
            return redirect()->route('notas.index')
                ->with('error', 'Error al eliminar las notas: ' . $e->getMessage());
        }
    }

    /**
     * Método para completar una nota
     */
    public function completar(Nota $nota)
    {
        try {
            // Verificar que la nota pertenece al usuario autenticado
            $this->authorize('update', $nota);

            // Marcar todas las actividades como completadas
            $nota->actividades()->update(['completada' => true]);

            $message = 'Todas las actividades marcadas como completadas.';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('notas.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al completar nota ID: {$nota->id} - Error: " . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al completar la nota: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('notas.index')
                ->with('error', 'Error al completar la nota: ' . $e->getMessage());
        }
    }

    /**
     * Método para marcar nota como pendiente
     */
    public function pendiente(Nota $nota)
    {
        try {
            // Verificar que la nota pertenece al usuario autenticado
            $this->authorize('update', $nota);

            // Marcar todas las actividades como pendientes
            $nota->actividades()->update(['completada' => false]);

            $message = 'Todas las actividades marcadas como pendientes.';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('notas.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al marcar nota como pendiente ID: {$nota->id} - Error: " . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al marcar la nota como pendiente: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('notas.index')
                ->with('error', 'Error al marcar la nota como pendiente: ' . $e->getMessage());
        }
    }

    /**
     * Método para cargar notas para pestañas (AJAX)
     */
    public function loadNotesForTabs()
    {
        try {
            $user = Auth::user();
            
            $notas = Nota::where('user_id', $user->id)
                        ->withCount(['actividades', 'recordatorios'])
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'notas' => $notas
            ]);

        } catch (\Exception $e) {
            Log::error("Error en loadNotesForTabs: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar las notas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método adicional: Eliminación forzada
     */
    public function forceDestroy($id)
    {
        try {
            $nota = Nota::withTrashed()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            Log::info("Iniciando eliminación FORZADA para nota ID: {$nota->id}");

            // Eliminar permanentemente
            $nota->forceDelete();

            Log::info("Nota ID: {$nota->id} eliminada permanentemente");
            return redirect()->route('notas.index')
                ->with('success', 'Nota eliminada permanentemente del sistema.');

        } catch (\Exception $e) {
            Log::error("Error en eliminación forzada - Nota ID: {$id} - Error: " . $e->getMessage());
            return redirect()->route('notas.index')
                ->with('error', 'Error al eliminar permanentemente la nota: ' . $e->getMessage());
        }
    }

    /**
     * Método adicional: Restaurar nota
     */
    public function restore($id)
    {
        try {
            $nota = Nota::withTrashed()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            Log::info("Restaurando nota ID: {$nota->id}");

            // Restaurar la nota
            $nota->restore();

            Log::info("Nota ID: {$nota->id} restaurada exitosamente");
            return redirect()->route('notas.index')
                ->with('success', 'Nota restaurada exitosamente.');

        } catch (\Exception $e) {
            Log::error("Error al restaurar nota ID: {$id} - Error: " . $e->getMessage());
            return redirect()->route('notas.index')
                ->with('error', 'Error al restaurar la nota: ' . $e->getMessage());
        }
    }

    /**
     * Método adicional: Obtener estadísticas del usuario
     */
    public function estadisticas()
    {
        try {
            $user = Auth::user();
            
            $estadisticas = [
                'total_notas' => Nota::where('user_id', $user->id)->count(),
                'total_actividades' => Actividad::whereHas('nota', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
                'actividades_completadas' => Actividad::whereHas('nota', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('completada', true)->count(),
                'total_recordatorios' => Recordatorio::whereHas('nota', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
                'notas_recientes' => Nota::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'estadisticas' => $estadisticas
                ]);
            }

            return view('notas.estadisticas', compact('estadisticas'));

        } catch (\Exception $e) {
            Log::error("Error al obtener estadísticas: " . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cargar estadísticas'
                ], 500);
            }

            return redirect()->route('notas.index')
                ->with('error', 'Error al cargar estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Método adicional: Buscar notas
     */
    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $request->get('q');

            $notas = Nota::where('user_id', $user->id)
                        ->where(function($q) use ($query) {
                            $q->where('titulo', 'LIKE', "%{$query}%")
                              ->orWhere('contenido', 'LIKE', "%{$query}%");
                        })
                        ->with(['actividades', 'recordatorios'])
                        ->withCount(['actividades', 'recordatorios'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

            // Calcular estadísticas para la vista
            $totalNotas = $notas->total();
            $totalActividades = Actividad::whereHas('nota', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            $actividadesCompletadas = Actividad::whereHas('nota', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('completada', true)->count();

            $totalRecordatorios = Recordatorio::whereHas('nota', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'notas' => $notas,
                    'resultados' => $notas->count()
                ]);
            }

            return view('notas.index', compact(
                'notas',
                'totalNotas',
                'totalActividades',
                'actividadesCompletadas',
                'totalRecordatorios'
            ))->with('query', $query)
              ->with('success', "Se encontraron {$notas->count()} resultados para '{$query}'");

        } catch (\Exception $e) {
            Log::error("Error en búsqueda de notas: " . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error en la búsqueda'
                ], 500);
            }

            return redirect()->route('notas.index')
                ->with('error', 'Error en la búsqueda: ' . $e->getMessage());
        }
    }

    /**
     * Método adicional: Alternar estado de completada
     */
    public function toggleCompletada(Nota $nota)
    {
        try {
            $this->authorize('update', $nota);

            // Verificar si todas las actividades están completadas
            $totalActividades = $nota->actividades()->count();
            $actividadesCompletadas = $nota->actividades()->where('completada', true)->count();

            if ($totalActividades > 0 && $actividadesCompletadas === $totalActividades) {
                // Si todas están completadas, marcarlas como pendientes
                $nota->actividades()->update(['completada' => false]);
                $message = 'Todas las actividades marcadas como pendientes.';
            } else {
                // Si no, marcarlas como completadas
                $nota->actividades()->update(['completada' => true]);
                $message = 'Todas las actividades marcadas como completadas.';
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al alternar estado de nota ID: {$nota->id} - Error: " . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cambiar estado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }
}
