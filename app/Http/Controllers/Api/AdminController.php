<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function metricas(Request $request)
    {
        Gate::authorize('ver-metricas');

        $totalEventos = Evento::count();
        $totalCupos = Evento::sum('cupo_maximo');
        $montoRecaudadoEstimado = $totalCupos * 50; 

        return response()->json([
            'total_eventos' => $totalEventos,
            'total_cupos_disponibles' => $totalCupos,
            'monto_global_recaudado' => $montoRecaudadoEstimado,
        ], 200);
    }
}