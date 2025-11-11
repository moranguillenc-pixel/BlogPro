<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nota;
use App\Models\Recordatorio;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function index()
    {
        try {
            // Cargar usuarios con sus notas activas y recordatorios
            $users = User::with(['notas', 'notas.recordatorio'])
                ->addSelect([
                    'total_notas' => Nota::selectRaw('count(*)')
                        ->whereColumn('user_id', 'users.id')
                        ->whereHas('recordatorio', fn($query) => 
                            $query->where('fecha_vencimiento', '>=', now())
                                  ->where('completado', false))
                ])
                ->get();

            // Debug: verificar que se están cargando los usuarios
            \Log::info('Usuarios cargados en NotaController: ' . $users->count());

            return view('notas.index', compact('users'));

        } catch (\Exception $e) {
            \Log::error('Error en NotaController index: ' . $e->getMessage());
            
            // Fallback seguro
            $users = User::with('notas.recordatorio')->get();
            return view('notas.index', compact('users'));
        }
    }

    /**
     * Método especial para cargar notas en pestañas/partials
     */
    public function loadNotesForTabs()
    {
        try {
            $users = User::with(['notas', 'notas.recordatorio'])
                ->addSelect([
                    'total_notas' => Nota::selectRaw('count(*)')
                        ->whereColumn('user_id', 'users.id')
                        ->whereHas('recordatorio', fn($query) => 
                            $query->where('fecha_vencimiento', '>=', now())
                                  ->where('completado', false))
                ])
                ->get();

            return view('notas.partials.notes-content', compact('users'));

        } catch (\Exception $e) {
            \Log::error('Error cargando notas para pestañas: ' . $e->getMessage());
            $users = User::with('notas.recordatorio')->get();
            return view('notas.partials.notes-content', compact('users'));
        }
    }

    // Mostrar formulario de creación
    public function create()
    {
        $users = User::all();
        return view('notas.create', compact('users'));
    }

    // Crear una nota con recordatorio
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'fecha_vencimiento' => 'required|date|after:now',
        ]);

        try {
            $note = Nota::create([
                'user_id' => $validated['user_id'],
                'titulo' => $validated['titulo'],
                'contenido' => $validated['contenido'],
            ]);

            $note->recordatorio()->create([
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'completado' => false,
            ]);

            return redirect()->route('notas.index')->with('success', 'Nota creada exitosamente!');

        } catch (\Exception $e) {
            \Log::error('Error creando nota: ' . $e->getMessage());
            return back()->with('error', 'Error al crear la nota: ' . $e->getMessage());
        }
    }

    // Mostrar formulario de edición
    public function edit(Nota $nota)
    {
        $users = User::all();
        return view('notas.edit', compact('nota', 'users'));
    }

    // Actualizar nota
    public function update(Request $request, Nota $nota)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'fecha_vencimiento' => 'required|date',
        ]);

        try {
            $nota->update([
                'user_id' => $validated['user_id'],
                'titulo' => $validated['titulo'],
                'contenido' => $validated['contenido'],
            ]);

            $nota->recordatorio()->update([
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
            ]);

            return redirect()->route('notas.index')->with('success', 'Nota actualizada exitosamente!');

        } catch (\Exception $e) {
            \Log::error('Error actualizando nota: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la nota: ' . $e->getMessage());
        }
    }

    // Eliminar nota
    public function destroy(Nota $nota)
    {
        try {
            $nota->delete();
            return redirect()->route('notas.index')->with('success', 'Nota eliminada exitosamente!');
        } catch (\Exception $e) {
            \Log::error('Error eliminando nota: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la nota: ' . $e->getMessage());
        }
    }

    // Marcar como completada
    public function completar(Nota $nota)
    {
        try {
            $nota->recordatorio()->update(['completado' => true]);
            return redirect()->route('notas.index')->with('success', 'Nota marcada como completada!');
        } catch (\Exception $e) {
            \Log::error('Error completando nota: ' . $e->getMessage());
            return back()->with('error', 'Error al marcar la nota como completada');
        }
    }
}