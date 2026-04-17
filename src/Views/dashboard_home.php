<?php
if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
$user = $_SESSION['user'];
?>

<div>
    <div class="dashboard-grid mt-4">

        <!-- Ficha Técnica -->
        <a href="/fichas-tecnicas"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-bold">Ficha Técnica</h3>
            <p class="text-sm opacity-80 mt-2">Crear y gestionar fichas técnicas.</p>
        </a>

        <!-- Procesos de Fabricación -->
        <a href="/procesos-ft"
            class="bg-gray-700 hover:bg-gray-800 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894
                       c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0
                       011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107
                       1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397
                       1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107
                       1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0
                       01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55
                       0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125
                       1.125 0 01-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125
                       1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272
                       1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            <h3 class="text-xl font-bold">Procesos de Fabricación</h3>
            <p class="text-sm opacity-80 mt-2">Configuración de procesos y movimientos.</p>
        </a>

        <!-- Clientes -->
        <a href="/clientes"
            class="bg-green-500 hover:bg-green-600 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
            </svg>
            <h3 class="text-xl font-bold">Clientes</h3>
            <p class="text-sm opacity-80 mt-2">Administrar información de clientes.</p>
        </a>

        <!-- Satélites -->
        <a href="/satelites"
            class="bg-purple-500 hover:bg-purple-600 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01
                       m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0
                       M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
            <h3 class="text-xl font-bold">Manejo de Satélites</h3>
            <p class="text-sm opacity-80 mt-2">Control y monitoreo de satélites.</p>
        </a>

        <!-- Orden de Pedido -->
        <a href="/orden-pedido"
            class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h3 class="text-xl font-bold">Orden de Pedido</h3>
            <p class="text-sm opacity-80 mt-2">Registrar y gestionar pedidos.</p>
        </a>

        <!-- Orden de Producción -->
        <a href="/orden-produccion"
            class="bg-red-500 hover:bg-red-600 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0
                       01 13.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5
                       3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125
                       1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <h3 class="text-xl font-bold">Orden de Producción</h3>
            <p class="text-sm opacity-80 mt-2">Planificación y control de producción.</p>
        </a>

        <!-- Seguimiento OPRs -->
        <a href="/seguimiento-opr"
            class="bg-indigo-500 hover:bg-indigo-600 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 17v-6h13v6H9zM9 17H4a1 1 0 01-1-1v-4a1 1 0 011-1h5"/>
            </svg>
            <h3 class="text-xl font-bold">Seguimiento OPRs</h3>
            <p class="text-sm opacity-80 mt-2">Control y monitoreo de órdenes.</p>
        </a>

        <!-- Avance en OPRs -->
        <a href="/orden-produccion/avance"
            class="bg-gray-500 hover:bg-gray-600 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75
                       C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75
                       8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25
                       c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z
                       M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21
                       4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0
                       01-1.125-1.125V4.125z"/>
            </svg>
            <h3 class="text-xl font-bold">Avance en OPRs</h3>
            <p class="text-sm opacity-80 mt-2">Procesos de órdenes de producción.</p>
        </a>

    </div>
</div>
