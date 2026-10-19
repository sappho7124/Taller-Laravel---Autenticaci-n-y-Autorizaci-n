<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EventHub') - EventHub Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">

    <!-- Barra de Navegación Global -->
    <nav class="bg-indigo-700 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <a href="{{ route('eventos.index') }}" class="font-bold text-xl tracking-tight">EventHub <span class="text-xs bg-indigo-900 px-2 py-0.5 rounded font-normal">UNAB API</span></a>
                <div class="space-x-1 text-sm font-medium">
                    <a href="{{ route('eventos.index') }}" class="px-3 py-1.5 rounded hover:bg-indigo-600 transition">🎟️ Eventos (Público & Policy)</a>
                    <a href="{{ route('login.view') }}" class="px-3 py-1.5 rounded hover:bg-indigo-600 transition">🔐 Login & Perfil</a>
                    <a href="{{ route('admin.metricas') }}" class="px-3 py-1.5 rounded hover:bg-indigo-600 transition">📊 Métricas (Gate Admin)</a>
                </div>
            </div>

            <!-- Estado de Sesión en el Navegador -->
            <div class="flex items-center space-x-3 text-sm">
                <div id="nav-guest" class="text-indigo-200 text-xs">Sin sesión activa</div>
                <div id="nav-user" class="hidden flex items-center space-x-2 bg-indigo-800 px-3 py-1 rounded-lg">
                    <span id="nav-user-name" class="font-semibold text-xs"></span>
                    <span id="nav-user-role" class="uppercase text-[10px] bg-amber-400 text-slate-900 font-bold px-1.5 py-0.5 rounded"></span>
                    <button onclick="cerrarSesionGlobal()" class="text-xs text-rose-300 hover:text-rose-100 underline ml-2">Salir</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal de cada Vista -->
    <main class="max-w-6xl w-full mx-auto p-6 flex-1 space-y-6">
        @yield('content')

        <!-- Consola Global de Respuestas HTTP (Muestra Códigos de Estado 200, 401, 403, 422) -->
        <div class="bg-slate-900 text-slate-100 p-4 rounded-xl shadow font-mono text-xs space-y-2 mt-8">
            <div class="flex justify-between items-center text-slate-400 border-b border-slate-700 pb-1">
                <span>Inspector de Respuesta HTTP de la API (RNF 3.1)</span>
                <span id="http-badge" class="font-bold text-slate-400">Sin peticiones aún</span>
            </div>
            <pre id="http-console" class="p-2 bg-slate-950 rounded max-h-48 overflow-x-auto text-emerald-400">Esperando interacción con la API...</pre>
        </div>
    </main>

    <!-- Script Global para Manejo de Tokens y Sesión -->
    <script>
        function getToken() {
            return localStorage.getItem('access_token') || '';
        }

        function setSession(token, user) {
            localStorage.setItem('access_token', token);
            localStorage.setItem('auth_user', JSON.stringify(user));
            actualizarEstadoSesion();
        }

        function cerrarSesionGlobal() {
            localStorage.removeItem('access_token');
            localStorage.removeItem('auth_user');
            actualizarEstadoSesion();
            logConsole(200, { message: "Sesión cerrada localmente. Ya no se enviará Token Bearer." });
        }

        function actualizarEstadoSesion() {
            const user = JSON.parse(localStorage.getItem('auth_user') || 'null');
            const guestEl = document.getElementById('nav-guest');
            const userEl = document.getElementById('nav-user');

            if (user) {
                guestEl.classList.add('hidden');
                userEl.classList.remove('hidden');
                document.getElementById('nav-user-name').innerText = user.name;
                document.getElementById('nav-user-role').innerText = user.role;
            } else {
                guestEl.classList.remove('hidden');
                userEl.classList.add('hidden');
            }
        }

        function logConsole(status, data) {
            const badge = document.getElementById('http-badge');
            const consoleEl = document.getElementById('http-console');

            badge.innerText = `Status: ${status}`;
            if (status >= 200 && status < 300) {
                badge.className = 'font-bold text-emerald-400';
                consoleEl.className = 'p-2 bg-slate-950 rounded max-h-48 overflow-x-auto text-emerald-400';
            } else if (status === 401 || status === 403) {
                badge.className = 'font-bold text-rose-400';
                consoleEl.className = 'p-2 bg-slate-950 rounded max-h-48 overflow-x-auto text-rose-400';
            } else {
                badge.className = 'font-bold text-amber-400';
                consoleEl.className = 'p-2 bg-slate-950 rounded max-h-48 overflow-x-auto text-amber-300';
            }

            consoleEl.innerText = JSON.stringify(data, null, 2);
        }

        document.addEventListener('DOMContentLoaded', actualizarEstadoSesion);
    </script>

    @yield('scripts')
</body>
</html>