<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub Solutions - Panel de Pruebas API</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 p-6">
    <div class="max-w-6xl mx-auto space-y-6">
        <header class="bg-indigo-700 text-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">EventHub Solutions API</h1>
                <p class="text-sm text-indigo-200">Universidad UNAB</p>
            </div>
            <div id="user-badge" class="hidden bg-indigo-900 px-4 py-2 rounded-lg text-sm">
                <span id="badge-name" class="font-bold"></span> 
                <span id="badge-role" class="ml-2 uppercase bg-indigo-500 text-xs px-2 py-0.5 rounded"></span>
                <button onclick="logout()" class="ml-4 text-rose-300 underline text-xs">Cerrar Sesión</button>
            </div>
        </header>

        <!-- Cuadro de credenciales sugeridas -->
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-xs grid grid-cols-2 md:grid-cols-4 gap-2">
            <div><strong>Admin:</strong> admin@eventhub.com</div>
            <div><strong>Organizador A:</strong> organizadorA@eventhub.com</div>
            <div><strong>Organizador B:</strong> organizadorB@eventhub.com</div>
            <div><strong>Asistente:</strong> asistente@eventhub.com</div>
            <div class="col-span-full text-slate-500">Contraseña común para todos: <code>password123</code></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- MÓDULO 1: Login & Perfil -->
            <div class="bg-white p-5 rounded-xl shadow space-y-4">
                <h2 class="font-semibold text-lg text-indigo-700 border-b pb-2">1. Autenticación (RF 1.1 / 1.2)</h2>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Email</label>
                    <input id="login-email" type="email" value="organizadorA@eventhub.com" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Password</label>
                    <input id="login-password" type="password" value="password123" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <div class="flex gap-2">
                    <button onclick="login()" class="w-full bg-indigo-600 text-white py-2 rounded text-sm hover:bg-indigo-700 font-medium">POST /api/login</button>
                    <button onclick="verPerfil()" class="w-full bg-slate-700 text-white py-2 rounded text-sm hover:bg-slate-800 font-medium">GET /api/perfil</button>
                </div>
            </div>

            <!-- MÓDULO 2: Edición con Policy -->
            <div class="bg-white p-5 rounded-xl shadow space-y-4">
                <h2 class="font-semibold text-lg text-indigo-700 border-b pb-2">2. Modificar Evento (Policy)</h2>
                <div>
                    <label class="block text-xs font-medium text-gray-700">ID del Evento</label>
                    <input id="edit-id" type="number" value="1" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Título</label>
                    <input id="edit-titulo" type="text" value="Conferencia Actualizada" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Cupo Máximo</label>
                    <input id="edit-cupo" type="number" value="300" class="w-full border rounded p-2 text-sm mt-1">
                </div>
                <button onclick="actualizarEvento()" class="w-full bg-emerald-600 text-white py-2 rounded text-sm hover:bg-emerald-700 font-medium">PUT /api/eventos/{id}</button>
            </div>

            <!-- MÓDULO 3: Métricas con Gate -->
            <div class="bg-white p-5 rounded-xl shadow space-y-4">
                <h2 class="font-semibold text-lg text-indigo-700 border-b pb-2">3. Admin Métricas (Gate)</h2>
                <p class="text-xs text-slate-500">Solo accesible para usuarios con rol <strong>admin</strong>.</p>
                <button onclick="consultarMetricas()" class="w-full bg-purple-600 text-white py-2 rounded text-sm hover:bg-purple-700 font-medium mt-8">GET /api/admin/metricas</button>
                <div id="metricas-display" class="hidden text-xs bg-purple-50 p-3 rounded border border-purple-200 space-y-1"></div>
            </div>
        </div>

        <!-- MÓDULO 2: Catálogo Público -->
        <div class="bg-white p-5 rounded-xl shadow space-y-3">
            <div class="flex justify-between items-center border-b pb-2">
                <h2 class="font-semibold text-lg text-indigo-700">Catálogo Público de Eventos (RF 2.1)</h2>
                <button onclick="cargarEventos()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-1 text-xs rounded">Refrescar Eventos</button>
            </div>
            <div id="eventos-list" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm"></div>
        </div>

        <!-- Consola de Respuestas HTTP -->
        <div class="bg-slate-900 text-slate-100 p-4 rounded-xl shadow font-mono text-xs space-y-2">
            <div class="flex justify-between items-center text-slate-400 border-b border-slate-700 pb-1">
                <span>Respuesta del Servidor (Status & JSON)</span>
                <span id="http-status" class="font-bold text-amber-400">Status: -</span>
            </div>
            <pre id="json-response" class="overflow-x-auto p-2 bg-slate-950 rounded max-h-56">Esperando peticiones...</pre>
        </div>
    </div>

    <script>
        let token = localStorage.getItem('token') || '';

        function setStatus(status, data) {
            const statusEl = document.getElementById('http-status');
            statusEl.innerText = `Status: ${status}`;
            if (status >= 200 && status < 300) statusEl.className = 'font-bold text-emerald-400';
            else if (status === 401 || status === 403) statusEl.className = 'font-bold text-rose-400';
            else statusEl.className = 'font-bold text-amber-400';

            document.getElementById('json-response').innerText = JSON.stringify(data, null, 2);
        }

        async function login() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            const res = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();
            setStatus(res.status, data);

            if (res.ok) {
                token = data.access_token;
                localStorage.setItem('token', token);
                document.getElementById('user-badge').classList.remove('hidden');
                document.getElementById('badge-name').innerText = data.user.name;
                document.getElementById('badge-role').innerText = data.user.role;
            }
        }

        async function verPerfil() {
            const res = await fetch('/api/perfil', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            setStatus(res.status, data);
        }

        async function cargarEventos() {
            const res = await fetch('/api/eventos', {
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();
            setStatus(res.status, json);

            const container = document.getElementById('eventos-list');
            container.innerHTML = '';
            (json.data || []).forEach(ev => {
                container.innerHTML += `
                    <div class="border p-3 rounded-lg bg-slate-50">
                        <div class="font-bold text-indigo-800">[ID: ${ev.id}] ${ev.titulo}</div>
                        <div class="text-xs text-slate-500">${ev.descripcion || 'Sin descripción'}</div>
                        <div class="mt-2 text-xs flex justify-between text-slate-600">
                            <span>Organizador: <strong>${ev.organizador ? ev.organizador.name : 'N/A'}</strong> (ID User: ${ev.user_id})</span>
                            <span>Cupos: <strong>${ev.cupo_maximo}</strong></span>
                        </div>
                    </div>
                `;
            });
        }

        async function actualizarEvento() {
            const id = document.getElementById('edit-id').value;
            const titulo = document.getElementById('edit-titulo').value;
            const cupo_maximo = document.getElementById('edit-cupo').value;

            const res = await fetch(`/api/eventos/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ titulo, cupo_maximo })
            });

            const data = await res.json();
            setStatus(res.status, data);
            if (res.ok) cargarEventos();
        }

        async function consultarMetricas() {
            const res = await fetch('/api/admin/metricas', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            setStatus(res.status, data);

            const display = document.getElementById('metricas-display');
            if (res.ok) {
                display.classList.remove('hidden');
                display.innerHTML = `
                    <div><strong>Total Eventos:</strong> ${data.total_eventos}</div>
                    <div><strong>Total Cupos:</strong> ${data.total_cupos_disponibles}</div>
                    <div><strong>Recaudo Global:</strong> $${data.monto_global_recaudado.toLocaleString()}</div>
                `;
            } else {
                display.classList.add('hidden');
            }
        }

        function logout() {
            localStorage.removeItem('token');
            token = '';
            document.getElementById('user-badge').classList.add('hidden');
            setStatus(200, { message: 'Sesión local cerrada' });
        }

        // Cargar eventos públicos al inicio
        cargarEventos();
    </script>
</body>
</html>