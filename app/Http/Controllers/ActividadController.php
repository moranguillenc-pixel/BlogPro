<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'todas');

        // SOLO actividades del usuario actual (evita duplicados y conteo erróneo)
        $baseQuery = Actividad::where('user_id', $user->id)
                              ->with('nota');

        // Aplicar filtro
        $query = match ($filter) {
            'pendientes'  => $baseQuery->where('completada', false),
            'completadas' => $baseQuery->where('completada', true),
            default       => $baseQuery,
        };

        // Orden correcto para MySQL
        $actividades = $query
            ->orderByRaw('fecha_limite IS NULL ASC')  // sin fecha al final
            ->orderBy('fecha_limite', 'ASC')
            ->orderByDesc('prioridad')
            ->paginate(15);

        // Contadores REALES y SIN duplicados
        $totalActividades = $baseQuery->count();
        $pendientesCount  = $baseQuery->clone()->where('completada', false)->count();
        $completadasCount = $baseQuery->clone()->where('completada', true)->count();

        return view('actividades.index', compact(
            'actividades',
            'filter',
            'totalActividades',
            'pendientesCount',
            'completadasCount'
        ));
    }

    public function create(Request $request)
    {
        $notas = Nota::where('user_id', Auth::id())->latest()->get();
        $nota = $request->nota_id ? Nota::find($request->nota_id) : null;

        return view('actividades.create', compact('notas', 'nota'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nota_id'      => 'nullable|exists:notas,id',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'fecha_limite' => 'nullable|date',
            'prioridad'    => 'required|in:1,2,3',
        ]);

        Actividad::create([
            'user_id'      => Auth::id(),
            'nota_id'      => $request->nota_id,
            'titulo'       => $validated['titulo'],
            'descripcion'  => $validated['descripcion'],
            'fecha_limite' => $validated['fecha_limite'],
            'prioridad'    => $validated['prioridad'],
            'completada'   => false,
        ]);

        return redirect()->route('actividades.index')->with('success', '¡Actividad creada!');
    }

    public function edit(Actividad $actividad)
    {
        $this->autorizarYCorregir($actividad);
        $actividad->loadMissing('nota');
        $notas = Nota::where('user_id', Auth::id())->latest()->get();

        return view('actividades.edit', compact('actividad', 'notas'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $this->autorizarYCorregir($actividad);

        $validated = $request->validate([
            'nota_id'      => 'nullable|exists:notas,id',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'fecha_limite' => 'nullable|date',
            'prioridad'    => 'required|in:1,2,3',
            'completada'   => 'sometimes|boolean',
        ]);

        $actividad->update([
            'nota_id'      => $request->filled('nota_id') ? $request->nota_id : $actividad->nota_id,
            'titulo'       => $validated['titulo'],
            'descripcion'  => $validated['descripcion'],
            'fecha_limite' => $validated['fecha_limite'],
            'prioridad'    => $validated['prioridad'],
            'completada'   => $request->has('completada') ? $request->boolean('completada') : $actividad->completada,
        ]);

        return redirect()->route('actividades.index')->with('success', '¡Actividad actualizada!');
    }

    public function destroy(Actividad $actividad)
    {
        $this->autorizarYCorregir($actividad);
        $actividad->delete();

        return back()->with('success', 'Actividad eliminada');
    }

    public function completar(Actividad $actividad)
    {
        $this->autorizarYCorregir($actividad);
        $actividad->update(['completada' => true]);

        return back()->with('success', '¡Marcada como completada!');
    }

    public function pendiente(Actividad $actividad)
    {
        $this->autorizarYCorregir($actividad);
        $actividad->update(['completada' => false]);

        return back()->with('success', '¡Marcada como pendiente!');
    }

    private function autorizarYCorregir(Actividad $actividad)
    {
        $userId = Auth::id();

        if (!$actividad->user_id) {
            $actividad->user_id = $userId;
        }

        if ($actividad->user_id !== $userId) {
            abort(403, 'No tienes permiso para esta acción');
        }
    }
}