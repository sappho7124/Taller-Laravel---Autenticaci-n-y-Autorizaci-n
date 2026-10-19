@extends('layouts.app')

@section('title', 'Módulo 1 - Identidad y Autenticación')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Formulario de Login -->
    <div class="bg-white p-6 rounded-xl shadow space-y-4">
        <div>
            <h2 class="text-xl font-bold text-indigo-700">Módulo 1: Inicio de Sesión</h2>
            <p class="text-xs text-slate-500">Endpoint: <code class="font-mono bg-slate-100 px-1 rounded">POST /api/login</code></p>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase">Email del Usuario</label>
                <input id="email" type="email" value="organizadorA@eventhub.com" class="w-full border rounded-lg p-2.5 text-sm mt-1 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase">Contraseña</label>
                <input id="password" type="password" value="password123" class="w-full border rounded-lg p-2.5 text-sm mt-1 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>
            <button onclick="hacerLogin()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow">
                Enviar Credenciales y Obtener Token
            </button>
        </div>

        <!-- Selector rápido de cuentas sembradas -->
        <div class="pt-3 border-t">
            <span class="text-xs text-slate-500 font-semibold block mb-2">Cargar credenciales de prueba:</span>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button onclick="llenar('admin@eventhub.com')" class="border p-1.5 rounded hover:bg-slate-50 text-left">👑 Admin</button>
                <button onclick="llenar('organizadorA@eventhub.com')" class="border p-1.5 rounded hover:bg-slate-50 text-left">🏢 Organizador A</button>
                <button onclick="llenar('organizadorB@eventhub.com')" class="border p-1.5 rounded hover:bg-slate-50 text-left">🏢 Organizador B</button>
                <button onclick="llenar('asistente@eventhub.com')" class="border p-1.5 rounded hover:bg-slate-50 text-left">👤 Asistente</button>
            </div>
        </div>
    </div>

    <!-- Consulta de Perfil Protegida -->
    <div class="bg-white p-6 rounded-xl shadow space-y-4">
        <div>
            <h2 class="text-xl font-bold text-indigo-700">Consulta de Perfil</h2>
            <p class="text-xs text-slate-500">Endpoint: <code class="font-mono bg-slate-100 px-1 rounded">GET /api/perfil</code> (Requiere Token Bearer)</p>
        </div>

        <p class="text-sm text-slate-600">
            Prueba la inspección de token con Sanctum. Si no has iniciado sesión o el token es inválido, retornará <strong>401 Unauthorized</strong>.
        </p>

        <button onclick="consultarPerfil()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition">
            Consultar Datos de Mi Perfil
        </button>

        <div id="perfil-card" class="hidden mt-4 p-4 rounded-lg bg-indigo-50 border border-indigo-200 text-sm space-y-1">
            <div><strong>ID de Usuario:</strong> <span id="perfil-id"></span></div>
            <div><strong>Nombre:</strong> <span id="perfil-name"></span></div>
            <div><strong>Email:</strong> <span id="perfil-email"></span></div>
            <div><strong>Rol Asignado:</strong> <span id="perfil-role" class="uppercase font-bold text-indigo-600"></span></div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function llenar(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password123';
    }

    async function hacerLogin() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();
        logConsole(res.status, data);

        if (res.ok) {
            setSession(data.access_token, data.user);
            alert('¡Inicio de sesión exitoso! Token Bearer guardado.');
        }
    }

    async function consultarPerfil() {
        const res = await fetch('/api/perfil', {
            headers: {
                'Authorization': `Bearer ${getToken()}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        logConsole(res.status, data);

        const card = document.getElementById('perfil-card');
        if (res.ok) {
            card.classList.remove('hidden');
            document.getElementById('perfil-id').innerText = data.user.id;
            document.getElementById('perfil-name').innerText = data.user.name;
            document.getElementById('perfil-email').innerText = data.user.email;
            document.getElementById('perfil-role').innerText = data.user.role;
        } else {
            card.classList.add('hidden');
        }
    }
</script>
@endsection