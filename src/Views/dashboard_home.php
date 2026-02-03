
<?php
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}
$user = $_SESSION['user'];
?>

<div class="dashboard-grid">
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

    <!-- Seguimiento OPs -->
    <a href="/seguimiento-op"
       class="bg-indigo-500 hover:bg-indigo-600 text-white rounded-2xl shadow-lg p-6
              flex flex-col items-center justify-center text-center
              transition transform hover:-translate-y-1">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-14 h-14 mb-4"
             fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="1.5">
            <path d="M11 17l-5-5m0 0l5-5m-5 5h12" />
        </svg>

        <h3 class="text-lg font-semibold">Seguimiento a OPs</h3>
        <p class="text-sm opacity-90 mt-1">
            Monitoreo de órdenes en proceso.
        </p>
    </a>

</div>
