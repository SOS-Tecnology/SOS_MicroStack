<?php
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}
$user = $_SESSION['user'];
?>


<div class="max-w-7xl mx-auto">
    <div class="dashboard-grid mt-6">
        <!-- Card -->
        <a href="/fichas-tecnicas"
            class="bg-blue-500 hover:bg-blue-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-10 h-10 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
            </svg>

            <h3 class="text-lg font-semibold">Ficha Técnica</h3>
            <p class="text-sm opacity-90 mt-1">
                Crear y gestionar fichas técnicas.
            </p>
        </a>
        <!-- Procesos de Fabricación -->
        <a href="/procesos-ft"
            class="bg-gray-700 hover:bg-gray-800 text-white rounded-2xl shadow-lg p-6
      flex flex-col items-center justify-center text-center
      transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4 text-white opacity-90"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round">

                <!-- dientes -->
                <path d="M12 3.5
             l1.2 0.3
             0.8 1.6
             1.6 0.7
             1.7 -0.4
             1.3 1.3
             -0.4 1.7
             0.7 1.6
             1.6 0.8
             0.3 1.2
             -0.3 1.2
             -1.6 0.8
             -0.7 1.6
             0.4 1.7
             -1.3 1.3
             -1.7 -0.4
             -1.6 0.7
             -0.8 1.6
             -1.2 0.3
             -1.2 -0.3
             -0.8 -1.6
             -1.6 -0.7
             -1.7 0.4
             -1.3 -1.3
             0.4 -1.7
             -0.7 -1.6
             -1.6 -0.8
             -0.3 -1.2
             0.3 -1.2
             1.6 -0.8
             0.7 -1.6
             -0.4 -1.7
             1.3 -1.3
             1.7 0.4
             1.6 -0.7
             0.8 -1.6
             z" />

                <!-- centro -->
                <circle cx="12" cy="12" r="3.2" />
            </svg>

            <h3 class="text-lg font-semibold">Procesos de Fabricación</h3>

            <p class="text-sm opacity-90 mt-1">
                Configuración de procesos, bodegas y movimientos
            </p>
        </a>

        <!-- Clientes -->
        <a href="/clientes"
            class="bg-green-500 hover:bg-green-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>

            <h3 class="text-lg font-semibold">Clientes</h3>
            <p class="text-sm opacity-90 mt-1">
                Administrar información de clientes.
            </p>
        </a>

        <!-- Satélites -->
        <a href="/satelites"
            class="bg-purple-500 hover:bg-purple-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path d="M12 4v16m8-8H4" />
            </svg>

            <h3 class="text-lg font-semibold">Manejo de Satélites</h3>
            <p class="text-sm opacity-90 mt-1">
                Control y monitoreo satelital.
            </p>
        </a>

        <!-- Orden de Pedido -->
        <a href="/orden-pedido"
            class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path d="M3 7h18M3 12h18M3 17h18" />
            </svg>

            <h3 class="text-lg font-semibold">Orden de Pedido</h3>
            <p class="text-sm opacity-90 mt-1">
                Registrar y gestionar pedidos.
            </p>
        </a>

        <!-- Orden de Producción -->
        <a href="/orden-produccion"
            class="bg-red-500 hover:bg-red-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path d="M9 17v-6h13v6H9z" />
            </svg>

            <h3 class="text-lg font-semibold">Orden de Producción</h3>
            <p class="text-sm opacity-90 mt-1">
                Planificación y control de producción.
            </p>
        </a>
        <!-- Seguimiento OPRs -->
        <a href="/seguimiento-opr"
            class="bg-indigo-500 hover:bg-indigo-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path d="M9 12h6m-6 4h6M5 6h14M4 6h16v14H4z" />
            </svg>

            <h3 class="text-lg font-semibold">Seguimiento OPRs</h3>
            <p class="text-sm opacity-90 mt-1">
                Control y monitoreo de órdenes de producción.
            </p>
        </a>

        <!-- Procesos a OPRs -->
        <a href="orden-produccion/avance"
            class="bg-gray-500 hover:bg-gray-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-14 h-14 mb-4"
                fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
            </svg>

            <h3 class="text-lg font-semibold">Avance en OPRs</h3>
            <p class="text-sm opacity-90 mt-1">
                Procesos de órdenes de producción.
            </p>
        </a>


    </div>
</div>