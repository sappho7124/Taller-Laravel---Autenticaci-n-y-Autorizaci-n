@extends('layouts.app')

@section('title', 'Módulo 2 - Eventos y Policy')

@section('content')
<div class="space-y-6">

    <!-- Encabezado explicativo -->
    <div class="bg-white p-5 rounded-xl shadow flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-indigo-700">Módulo 2: Catálogo y Gestión de Eventos</h2>
            <p class="text-xs text-slate-500">Demostración de <strong>Policy (RNF 2.1)</strong>: Solo el organizador propietario puede modificar su evento.</p>
        </div>
        <button onclick="cargarCatalogo()" class="bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-semibold">
            🔄 Recargar Catálogo (GET /api/eventos)
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Listado Público de Eventos (RF 2.1) -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-bold text-sm text-slate-700 uppercase tracking-wide">Eventos Registrados en la Plataforma</h3>
            <div id="contenedor-eventos" class="grid grid-cols-1 gap-3">
                <p class="text-slate-400 text-sm">Cargando eventos...</p>
            </div>
        </div>

        <!-- Formulario de Modificación de Evento (RF 2.2 y 2.3 con Policy) -->
        <div class="bg-white p-5 rounded-xl shadow space-y-4 h-fit border-t-4 border-emerald-500">
            <div>
                <h3 class="font-bold text-base text-slate-800">Modificar Evento</h3>
                <p class="text-xs text-slate-500 font-mono">PUT /api/eventos/{id}</p>
            </div>

            <div class="bg-amber-50 p-2.5 rounded text-xs text-amber-800 border border-amber-200">
                💡 <strong>Regla de Autorización:</strong> Si intentas editar un evento siendo <em>Organizador B</em> o <em>Asistente</em> sobre un evento del <em>Organizador A</em>, la Policy retornará <strong>403 Forbidden</strong>.
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600">ID del Evento</label>
                    <input id="edit-id" type="number" value="1" class="w-full border rounded p-2 text-sm mt-1 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600">Nuevo Título</label>
                    <input id="edit-titulo" type="text" value="Conferencia Tech Summit 2026 (Actualizada)" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600">Cupo Máximo</label>
                    <input id="edit-cupo" type="number" value="350" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600">Descripción (Opcional)</label>
                    <textarea id="edit-desc" rows="2" class="w-full border rounded p-2 text-sm mt-1">Edición actualizada con nuevos ponentes internacionales.</textarea>
                </div>
                <button onclick="guardarCambiosEvento()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 rounded text-sm transition">
                    Ejecutar Actualización (Policy)
                </button>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    async function cargarCatalogo() {
        const res = await fetch('/api/eventos', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        logConsole(res.status, data);

        const container = document.getElementById('contenedor-eventos');
        container.innerHTML = '';

        if (!data.data || data.data.length === 0) {
            container.innerHTML = '<p class="text-sm text-slate-500">No hay eventos registrados.</p>';
            return;
        }

        data.data.forEach(evento => {
            const orgName = evento.organizador ? evento.organizador.name : 'Desconocido';
            const orgEmail = evento.organizador ? evento.organizador.email : '';

            container.innerHTML += `
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex justify-between items-start">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="bg-indigo-100 text-indigo-700 font-mono text-xs px-2 py-0.5 rounded font-bold">ID: ${evento.id}</span>
                            <h4 class="font-bold text-slate-900">${evento.titulo}</h4>
                        </div>
                        <p class="text-xs text-slate-600">${evento.descripcion || 'Sin descripción'}</p>
                        <div class="text-xs text-slate-500 pt-1 flex items-center space-x-4">
                            <span>👤 Creador: <strong>${orgName}</strong> (User ID: ${evento.user_id})</span>
                            <span>🎟️ Cupos: <strong>${evento.cupo_maximo}</strong></span>
                        </div>
                    </div>
                    <button onclick="seleccionarParaEditar(${evento.id}, '${evento.titulo}', ${evento.cupo_maximo}, '${evento.descripcion || ''}')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-3 py-1.5 rounded transition">
                        Cargar en Formulario
                    </button>
                </div>
            `;
        });
    }

    function seleccionarParaEditar(id, titulo, cupo, descripcion) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-titulo').value = titulo;
        document.getElementById('edit-cupo').value = cupo;
        document.getElementById('edit-desc').value = descripcion;
    }

    async function guardarCambiosEvento() {
        const id = document.getElementById('edit-id').value;
        const titulo = document.getElementById('edit-titulo').value;
        const cupo_maximo = document.getElementById('edit-cupo').value;
        const descripcion = document.getElementById('edit-desc').value;

        const res = await fetch(`/api/eventos/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${getToken()}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ titulo, cupo_maximo, descripcion })
        });

        const data = await res.json();
        logConsole(res.status, data);

        if (res.ok) {
            cargarCatalogo();
        }
    }

    document.addEventListener('DOMContentLoaded', cargarCatalogo);
</script>
@endsection