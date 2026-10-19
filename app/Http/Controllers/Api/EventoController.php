<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with(['organizador:id,name,email'])->get();

        return response()->json([
            'data' => $eventos
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        Gate::authorize('update', $evento);

        $validated = $request->validate([
            'titulo' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'cupo_maximo' => 'required|integer|min:1',
        ]);

        $evento->update($validated);

        return response()->json([
            'message' => 'Evento actualizado exitosamente',
            'data' => $evento
        ], 200);
    }
}