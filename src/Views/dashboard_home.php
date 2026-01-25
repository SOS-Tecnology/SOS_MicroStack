<?php
$title = "Dashboard";
ob_start();
?>

<main class="flex-1 p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Botón Ficha Técnica -->
    <a href="/fichas-tecnicas" 
       class="bg-gradient-to-r from-blue-500 to-blue-700 shadow-lg rounded-2xl py-3 flex flex-col items-center text-white hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-1/2 h-1/2 mb-2"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold">Ficha Técnica</h3>
        <p class="text-xs text-gray-100 text-center">Crear y gestionar fichas técnicas.</p>
    </a>

    <!-- Botón Clientes -->
    <a href="/clientes" 
       class="bg-gradient-to-r from-green-400 to-green-600 shadow-lg rounded-2xl py-3 flex flex-col items-center text-white hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-1/2 h-1/2 mb-2"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
        </svg>
        <h3 class="text-lg font-semibold">Clientes</h3>
        <p class="text-xs text-gray-100 text-center">Administrar información de clientes.</p>
    </a>

    <!-- Botón Manejo de Satélites -->
    <a href="/satelites" 
       class="bg-gradient-to-r from-purple-500 to-purple-700 shadow-lg rounded-2xl py-3 flex flex-col items-center text-white hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-1/2 h-1/2 mb-2"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M12 4v16m8-8H4"/>
        </svg>
        <h3 class="text-lg font-semibold">Manejo de Satélites</h3>
        <p class="text-xs text-gray-100 text-center">Control y monitoreo satelital.</p>
    </a>

    <!-- Botón Orden de Pedido -->
    <a href="/orden-pedido" 
       class="bg-gradient-to-r from-yellow-400 to-yellow-600 shadow-lg rounded-2xl py-3 flex flex-col items-center text-white hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-1/2 h-1/2 mb-2"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M3 7h18M3 12h18M3 17h18"/>
        </svg>
        <h3 class="text-lg font-semibold">Orden de Pedido</h3>
        <p class="text-xs text-gray-100 text-center">Registrar y gestionar pedidos.</p>
    </a>

    <!-- Botón Orden de Producción -->
    <a href="/orden-produccion" 
       class="bg-gradient-to-r from-red-500 to-red-700 shadow-lg rounded-2xl py-3 flex flex-col items-center text-white hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-1/2 h-1/2 mb-2"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M9 17v-6h13v6H9z"/>
        </svg>
        <h3 class="text-lg font-semibold">Orden de Producción</h3>
        <p class="text-xs text-gray-100 text-center">Planificación y control de producción.</p>
    </a>

    <!-- Botón Seguimiento a OPs -->
    <a href="/seguimiento-op" 
       class="bg-gradient-to-r from-indigo-500 to-indigo-700 shadow-lg rounded-2xl py-3 flex flex-col items-center text-white hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-1/2 h-1/2 mb-2"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
        </svg>
        <h3 class="text-lg font-semibold">Seguimiento a OPs</h3>
        <p class="text-xs text-gray-100 text-center">Monitoreo de órdenes en proceso.</p>
    </a>
</main>

<?php
$content = ob_get_clean();
include __DIR__ . "/layouts/dashboard.php"; // ✅ ruta corregida
?>
