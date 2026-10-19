@extends('layouts.app')

@section('title', 'Módulo 3 - Métricas y Reportes con Gate')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white p-6 rounded-xl shadow space-y-4">
        <div>
            <h2 class="text-xl font-bold text-indigo-700">Módulo 3: Métricas y Reportes Globales</h2>
            <p class="text-xs text-slate-500">Demostración de <strong>Gate (RNF 2.2)</strong>: Solo usuarios con rol <code class="font-mono bg-slate-100 px-1 rounded text-purple-700 font-bold">admin</code> pueden acceder.</p>
        </div>

        <div class="bg-purple-50 border border-purple-200 text-purple-900 p-4 rounded-lg text-xs leading-relaxed">
            🛡️ <strong>Regla de Autorización con Gate:</strong>
            El Gate <code>ver-metricas</code> está desacoplado del controlador y registrado en <code>AppServiceProvider</code>.
            Si el usuario autenticado tiene el rol <em>organizador</em> o <em>asistente</em>, la API responderá con código <strong>403 Forbidden</strong>.
        </div>

        <button onclick="solicitarMetricas()" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 rounded-lg text-sm transition shadow flex justify-center items-center space-x-2">
            <span>Consultar Métricas Globales (GET /api/admin/metricas)</span>
        </button>

        <!-- Tarjetas de Métricas -->
        <div id="panel-kpis" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                <span class="text-xs text-slate-500 uppercase font-semibold">Total de Eventos</span>
                <div id="kpi-eventos" class="text-3xl font-extrabold text-indigo-600 mt-1">0</div>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                <span class="text-xs text-slate-500 uppercase font-semibold">Cupos Totales</span>
                <div id="kpi-cupos" class="text-3xl font-extrabold text-slate-800 mt-1">0</div>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                <span class="text-xs text-slate-500 uppercase font-semibold">Recaudo Estimado</span>
                <div id="kpi-recaudo" class="text-3xl font-extrabold text-emerald-600 mt-1">$0</div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    async function solicitarMetricas() {
        const res = await fetch('/api/admin/metricas', {
            headers: {
                'Authorization': `Bearer ${getToken()}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        logConsole(res.status, data);

        const kpis = document.getElementById('panel-kpis');
        if (res.ok) {
            kpis.classList.remove('hidden');
            document.getElementById('kpi-eventos').innerText = data.total_eventos;
            document.getElementById('kpi-cupos').innerText = data.total_cupos_disponibles;
            document.getElementById('kpi-recaudo').innerText = `$${Number(data.monto_global_recaudado).toLocaleString()}`;
        } else {
            kpis.classList.add('hidden');
        }
    }
</script>
@endsection